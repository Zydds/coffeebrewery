<?php

namespace App\Services\Cafes;

use App\Models\Cafe;
use App\Models\CafeArea;
use App\Models\Wilayah;

/**
 * Pulls Overpass data into the cafes and cafe_areas tables.
 *
 * The rules that matter: rows are upserted by OSM id, cafés missing from a
 * later run are flagged (never deleted), and imported place nodes never
 * overwrite a curated alias.
 */
class CafeImporter
{
    // OSM place types that make sense as in-map areas. 'city' is excluded on
    // purpose: the kota itself is already the region selector, so a place=city
    // node would just duplicate it in the dropdown.
    private const AREA_PLACE_TYPES = [
        'neighbourhood', 'suburb', 'quarter', 'village', 'hamlet',
        'locality', 'town', 'municipality', 'city_district',
    ];

    public function __construct(
        private OverpassClient $overpass
    ) {}

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
     *
     * @return array{id: ?string, candidates: int, resolved: bool}
     */
    public function resolveAreaId(Wilayah $region, ?string $forcedAreaId = null): array
    {
        if ($forcedAreaId) {
            return ['id' => $this->normalizeAreaId($forcedAreaId), 'candidates' => 0, 'resolved' => false];
        }

        if ($region->osm_area_id) {
            return ['id' => $region->osm_area_id, 'candidates' => 0, 'resolved' => false];
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

                foreach ($this->overpass->query($q)['elements'] ?? [] as $el) {
                    $candidates[$el['id']] = $el['tags'];
                }

                sleep(2);
            }
        }

        if (! $candidates) {
            return ['id' => null, 'candidates' => 0, 'resolved' => false];
        }

        $ids = array_keys($candidates);
        $id = $this->normalizeAreaId((string) $ids[0]);
        $region->update(['osm_area_id' => $id]);

        return ['id' => $id, 'candidates' => count($ids), 'resolved' => true];
    }

    /**
     * @return array{seen: int, new: int, flagged: int}
     */
    public function importCafes(Wilayah $region, string $areaId): array
    {
        $runStart = now();

        $q = sprintf(
            '[out:json][timeout:180];area(%s)->.a;nwr["amenity"="cafe"](area.a);out meta center;',
            $areaId
        );

        $elements = $this->overpass->query($q)['elements'] ?? [];
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

        return ['seen' => $seen, 'new' => $inserted, 'flagged' => $flagged];
    }

    public function importAreas(Wilayah $region, string $areaId): int
    {
        $q = sprintf(
            '[out:json][timeout:120];area(%s)->.a;node["place"](area.a);out;',
            $areaId
        );

        $elements = $this->overpass->query($q)['elements'] ?? [];
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

        return $kept;
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

    /** Overpass string literal: double-quoted, inner quotes escaped. */
    private function quote(string $value): string
    {
        return '"'.str_replace('"', '\\"', $value).'"';
    }
}
