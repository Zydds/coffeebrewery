<?php

namespace App\Console\Commands;

use App\Models\Cafe;
use App\Models\CafeArea;
use App\Models\Wilayah;
use App\Services\Cafes\AreaAssigner;
use App\Services\Cafes\CafeImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ImportCafes extends Command
{
    protected $signature = 'import:cafes
                            {region? : Kode wilayah to import (e.g. 31.74). Comma-separate for several.}
                            {--all : Import every kabupaten/kota that already has an OSM area resolved}
                            {--area-id= : Force an Overpass area id instead of resolving by name}
                            {--skip-areas : Skip the place-node/area pass}';

    protected $description = 'Import amenity=cafe from OpenStreetMap into the cafes table (idempotent upsert, flag-not-delete)';

    public function handle(CafeImporter $importer, AreaAssigner $assigner): int
    {
        $regions = $this->resolveRegions();

        if ($regions->isEmpty()) {
            $this->error('No regions matched. Pass a kode wilayah (e.g. 31.74) or --all.');

            return self::FAILURE;
        }

        $forcedAreaId = $this->option('area-id') ? (string) $this->option('area-id') : null;

        foreach ($regions as $region) {
            $this->importRegion($importer, $assigner, $region, $forcedAreaId);
        }

        return self::SUCCESS;
    }

    /** @return Collection<int, Wilayah> */
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

    private function importRegion(
        CafeImporter $importer,
        AreaAssigner $assigner,
        Wilayah $region,
        ?string $forcedAreaId
    ): void {
        $this->newLine();
        $this->line("<info>=== {$region->nama} ({$region->kode}) ===</info>");

        try {
            $resolution = $importer->resolveAreaId($region, $forcedAreaId);
        } catch (\RuntimeException $e) {
            $this->error("  Area resolution failed: {$e->getMessage()}");

            return;
        }

        if ($resolution['id'] === null) {
            $this->error('  No OSM admin boundary found for this name. Fix upstream or pass --area-id.');

            return;
        }

        if ($resolution['candidates'] > 1) {
            $this->warn(sprintf(
                '  %d boundary candidates for %s — using %s. Verify on osm.org or pass --area-id.',
                $resolution['candidates'],
                $region->nama,
                $resolution['id']
            ));
        }

        if ($resolution['resolved']) {
            $this->line("  Resolved boundary \"{$region->nama}\" -> area {$resolution['id']}");
        }

        $stats = $importer->importCafes($region, $resolution['id']);
        $this->line("  Cafes: {$stats['seen']} fetched, {$stats['new']} new, {$stats['flagged']} flagged missing");

        if (! $this->option('skip-areas')) {
            $kept = $importer->importAreas($region, $resolution['id']);
            $this->line("  Areas: {$kept} place nodes");

            $assigned = $assigner->assign($region);
            $radius = config('cafes.area_radius_m', 1000);
            $this->line("  Assigned: {$assigned} cafes to a place node ({$radius}m radius)");
        }

        $this->report($region);
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
}
