<?php

namespace App\Console\Commands;

use App\Models\Cafe;
use App\Models\CafeArea;
use App\Models\Wilayah;
use App\Services\Cafes\OverpassClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCafes extends Command
{
    protected $signature = 'import:cafes
                            {region? : Kode wilayah to import (e.g. 31.74). Comma-separate for several.}
                            {--all : Import every kabupaten/kota that already has an OSM area resolved}
                            {--area-id= : Force an Overpass area id instead of resolving by name}
                            {--skip-areas : Skip the place-node/area pass}';

    protected $description = 'Import amenity=cafe from OpenStreetMap into the cafes table (idempotent upsert, flag-not-delete)';

    // OSM place types that make sense as in-map areas. 'city' is excluded on
    // purpose: the kota itself is already the region selector, so a place=city
    // node would just duplicate it in the dropdown.
    private const AREA_PLACE_TYPES = [
        'neighbourhood', 'suburb', 'quarter', 'village', 'hamlet',
        'locality', 'town', 'municipality', 'city_district',
    ];

    public function handle(OverpassClient $overpass): int
    {
        $regions = $this->resolveRegions();

        if ($regions->isEmpty()) {
            $this->error('No regions matched. Pass a kode wilayah (e.g. 31.74) or --all.');

            return self::FAILURE;
        }

        foreach ($regions as $region) {
            $this->importRegion($overpass, $region);
        }

        return self::SUCCESS;
    }

    /** @return \Illuminate\Support\Collection<int, Wilayah> */
    private function resolveRegions()
    {
        if ($this->option('all')) {
            return Wilayah::kabupatenKota()
                ->whereNotNull('osm_area_id')
                ->orderBy('kode')
                ->get();
        }

        $codes = array_filter(array_map('trim', explode(',', (string) $this->argument('region'))));

        return Wilayah::kabupatenKota()
            ->whereIn('kode', $codes)
            ->orderBy('kode')
            ->get();
    }

    private function importRegion(OverpassClient $overpass, Wilayah $region): void
    {
        $this->newLine();
        $this->line("<info>=== {$region->nama} ({$region->kode}) ===</info>");

        try {
            $areaId = $this->resolveAreaId($overpass, $region);
        } catch (\RuntimeException $e) {
            $this->error("  Area resolution failed: {$e->getMessage()}");

            return;
        }

        if ($areaId === null) {
            $this->error('  No OSM admin boundary found for this name. Fix upstream or pass --area-id.');

            return;
        }

        $this->importCafes($overpass, $region, $areaId);

        if (! $this->option('skip-areas')) {
            $this->importAreas($overpass, $region, $areaId);
            $this->assignAreas($region);
        }

        $this->report($region);
    }

    /**
     * Resolve a kode wilayah to an Overpass area id via the official name.
     *
     * OSM tagging varies by region: some boundaries carry the full official
     * name as `name` ("Kota Bandung"), others the short form with the full
     * one in `official_name` ("Jakarta Selatan" /
     * "Kota Administrasi Jakarta Selatan"). So we try every name variant
     * against both keys, scoped to admin_level=5 (kabupaten/kota in
     * Indonesia — verified against the three pilot regions; without it,
     * generic names like "Bandung" match unrelated desa at level 7).
     */
    private function resolveAreaId(OverpassClient $overpass, Wilayah $region): ?string
    {
        if ($this->option('area-id')) {
            return $this->normalizeAreaId((string) $this->option('area-id'));
        }

        if ($region->osm_area_id) {
            return $region->osm_area_id;
        }

        $variants = [$region->nama];

        $short = preg_replace('/^(Kota|Kabupaten)\s+(Administrasi\s+)?/u', '', $region->nama, 1, $stripped);

        if ($stripped && $short !== '' && $short !== $region->nama) {
            $variants[] = $short;
        }

        $candidates = [];

        foreach (array_unique($variants) as $name) {
            foreach (['name', 'official_name'] as $key) {
                $q = sprintf(
                    '[out:json][timeout:60];area["boundary"="administrative"]["admin_level"="5"]["%s"=%s];out ids tags;',
                    $key,
                    $this->quote($name)
                );

                foreach ($overpass->query($q)['elements'] ?? [] as $el) {
                    $candidates[$el['id']] = $el['tags'];
                }

                sleep(2);
            }
        }

        if (! $candidates) {
            return null;
        }

        $ids = array_keys($candidates);

        if (count($ids) > 1) {
            $this->warn(sprintf(
                '  %d boundary candidates for %s — using %d. Verify on osm.org or pass --area-id.',
                count($ids),
                $region->nama,
                $ids[0]
            ));
        }

        $id = $this->normalizeAreaId((string) $ids[0]);
        $region->update(['osm_area_id' => $id]);
        $this->line("  Resolved boundary \"{$region->nama}\" -> area {$id}");

        return $id;
    }

    /**
     * Overpass serializes some area ids through a 32-bit overflow in its
     * JSON output (e.g. area 3605802438 prints as -689164858). `area(id:)`
     * wants the positive form: add 2^32 back to negative ids.
     */
    private function normalizeAreaId(string $id): string
    {
        if (str_starts_with($id, '-')) {
            return (string) ((int) $id + 4294967296);
        }

        return $id;
    }

    private function importCafes(OverpassClient $overpass, Wilayah $region, string $areaId): void
    {
        $runStart = now();

        $q = sprintf(
            '[out:json][timeout:180];area(%s)->.a;nwr["amenity"="cafe"](area.a);out meta center;',
            $areaId
        );

        $elements = $overpass->query($q)['elements'] ?? [];
        $seen = 0;
        $inserted = 0;

        foreach ($elements as $el) {
            // Ways/relations have no own lat/lon — out meta center provides
            // the center point, which is what the map pin uses.
            $lat = $el['lat'] ?? $el['center']['lat'] ?? null;
            $lng = $el['lon'] ?? $el['center']['lon'] ?? null;

            if ($lat === null || $lng === null) {
                continue;
            }

            $tags = $el['tags'] ?? [];
            $name = trim($tags['name'] ?? '');

            if ($name === '') {
                // A pin with no name has nothing to show on a card.
                continue;
            }

            // Upsert by OSM id; last_seen_at marks this run so cafés absent
            // from a later run can be flagged (never deleted).
            $cafe = Cafe::updateOrCreate(
                ['osm_id' => $el['id'], 'osm_type' => $el['type']],
                [
                    'name' => $name,
                    'lat' => $lat,
                    'lng' => $lng,
                    'region_kode' => $region->kode,
                    'opening_hours' => $tags['opening_hours'] ?? null,
                    'addr_street' => $tags['addr:street'] ?? null,
                    'addr_housenumber' => $tags['addr:housenumber'] ?? null,
                    'addr_city' => $tags['addr:city'] ?? null,
                    'tags' => $tags,
                    'osm_timestamp' => $el['timestamp'] ?? null,
                    'last_seen_at' => $runStart,
                    'is_missing' => false,
                ]
            );

            $inserted += $cafe->wasRecentlyCreated ? 1 : 0;
            $seen++;
        }

        // Flag-not-delete: anything in this region not seen in this run is
        // marked missing, protecting against a flaky response wiping rows.
        $flagged = Cafe::where('region_kode', $region->kode)
            ->where('last_seen_at', '<', $runStart)
            ->where('is_missing', false)
            ->update(['is_missing' => true]);

        $this->line("  Cafes: {$seen} fetched, {$inserted} new, {$flagged} flagged missing");
    }

    private function importAreas(OverpassClient $overpass, Wilayah $region, string $areaId): void
    {
        $q = sprintf(
            '[out:json][timeout:120];area(%s)->.a;node["place"](area.a);out;',
            $areaId
        );

        $elements = $overpass->query($q)['elements'] ?? [];
        $kept = 0;

        foreach ($elements as $el) {
            $tags = $el['tags'] ?? [];
            $type = $tags['place'] ?? '';
            $name = trim($tags['name'] ?? '');

            // `out tags;` omits geometry — `out;` gives node lat/lon, but
            // guard anyway so a malformed element can't abort the region.
            if ($name === '' || ! in_array($type, self::AREA_PLACE_TYPES, true)
                || ! isset($el['lat'], $el['lon'])) {
                continue;
            }

            CafeArea::updateOrCreate(
                ['osm_id' => $el['id'], 'region_kode' => $region->kode],
                [
                    'name' => $name,
                    'place_type' => $type,
                    'lat' => $el['lat'],
                    'lng' => $el['lon'],
                    // A re-import must not turn a curated alias into a plain
                    // row if OSM later adds a node with the same name.
                    'is_alias' => false,
                ]
            );

            $kept++;
        }

        $this->line("  Areas: {$kept} place nodes");
    }

    /**
     * Assign each café to its nearest place node within the configured
     * radius. Haversine in PHP — a region is a few hundred cafés times a
     * few hundred place nodes, which is well under a second.
     */
    private function assignAreas(Wilayah $region): void
    {
        $radius = (float) config('cafes.area_radius_m', 1000);

        $areas = CafeArea::where('region_kode', $region->kode)->get(['name', 'lat', 'lng']);
        $assigned = 0;

        Cafe::where('region_kode', $region->kode)
            ->where('is_missing', false)
            ->orderBy('id')
            ->chunkById(200, function ($cafes) use ($areas, $radius, &$assigned) {
                foreach ($cafes as $cafe) {
                    $best = null;
                    $bestDist = $radius;

                    foreach ($areas as $area) {
                        $d = $this->haversine($cafe->lat, $cafe->lng, $area->lat, $area->lng);

                        if ($d < $bestDist) {
                            $bestDist = $d;
                            $best = $area->name;
                        }
                    }

                    if ($best !== null && $best !== $cafe->area_name) {
                        $cafe->update(['area_name' => $best]);
                    }

                    if ($best !== null) {
                        $assigned++;
                    }
                }
            });

        $this->line("  Assigned: {$assigned} cafes to a place node ({$radius}m radius)");
    }

    private function report(Wilayah $region): void
    {
        $cafes = Cafe::where('region_kode', $region->kode)->where('is_missing', false);

        [$total, $hours, $addr] = [
            (clone $cafes)->count(),
            (clone $cafes)->whereNotNull('opening_hours')->count(),
            (clone $cafes)->whereNotNull('addr_street')->count(),
        ];

        $pct = fn (int $n) => $total ? round(100 * $n / $total).'%' : 'n/a';

        // Year histogram in PHP (portable across pgsql/sqlite; a region is a
        // few hundred rows, so no need for a driver-specific date function).
        $freshness = (clone $cafes)
            ->pluck('osm_timestamp')
            ->filter()
            ->countBy(fn ($ts) => (int) $ts->format('Y'))
            ->sortDesc()
            ->take(5);

        $areas = CafeArea::where('region_kode', $region->kode)->count();
        $withArea = (clone $cafes)->whereNotNull('area_name')->count();

        $this->table(
            ['Cafes', '…with hours', '…with address', 'Areas', '…assigned'],
            [[$total, "{$hours} ({$pct($hours)})", "{$addr} ({$pct($addr)})", $areas, "{$withArea} ({$pct($withArea)})"]]
        );

        $this->line('  Freshness (OSM edit year): '.$freshness->map(fn ($c, $y) => substr((string) $y, 0, 4).':'.$c)->implode(', '));
    }

    /** Overpass string literal: double-quoted, inner quotes escaped. */
    private function quote(string $value): string
    {
        return '"'.str_replace('"', '\\"', $value).'"';
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(sqrt($a));
    }
}
