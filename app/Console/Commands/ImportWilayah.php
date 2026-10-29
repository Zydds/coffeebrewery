<?php

namespace App\Console\Commands;

use App\Models\Wilayah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportWilayah extends Command
{
    protected $signature = 'wilayah:import
                            {--file= : Local path to wilayah.sql (downloads the default dataset when omitted)}';

    protected $description = 'Import official Kemendagri kode wilayah (provinsi + kabupaten/kota) for the cafe cascade';

    // Kepmendagri No. 300.2-2138/2025, MIT-licensed mirror. Levels 1-2 only
    // (~552 rows) — that's all the provinsi -> kabupaten/kota cascade needs;
    // kecamatan level lives as OSM place nodes in cafe_areas.
    public const DATASET_URL = 'https://raw.githubusercontent.com/cahyadsn/wilayah/master/db/wilayah.sql';

    public function handle(): int
    {
        $file = $this->option('file') ?: $this->downloadDataset();

        if ($file === null) {
            return self::FAILURE;
        }

        $sql = File::get($file);

        // Rows look like: ('31.74','Kota Jakarta Selatan')
        // Level = dot count: '31' = provinsi, '31.74' = kabupaten/kota.
        preg_match_all("/\('(\d{2}(?:\.\d{2}){0,2})','((?:[^'\\\\]|\\\\.)*)'\)/u", $sql, $matches, PREG_SET_ORDER);

        $rows = [];
        $skipped = 0;

        foreach ($matches as [, $kode, $nama]) {
            $depth = substr_count($kode, '.');

            if ($depth > 1) {
                // Kecamatan/kelurahan level: not needed for the cascade.
                $skipped++;

                continue;
            }

            $nama = str_replace(["\\'", '\\\\'], ["'", '\\'], trim($nama));

            $rows[$kode] = [
                'kode' => $kode,
                'nama' => $nama,
                'level' => $depth === 0
                    ? Wilayah::LEVEL_PROVINSI
                    : Wilayah::LEVEL_KABUPATEN_KOTA,
                'parent_kode' => $depth === 0 ? null : substr($kode, 0, 2),
                // Preserve a previously resolved OSM area id across re-imports.
                'osm_area_id' => Wilayah::find($kode)?->osm_area_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Wilayah::upsert(
            array_values($rows),
            ['kode'],
            ['nama', 'level', 'parent_kode', 'osm_area_id', 'updated_at']
        );

        $this->info(sprintf(
            'Imported %d wilayah entries (%d provinsi, %d kabupaten/kota; %d deeper levels skipped).',
            count($rows),
            count($rows) - count(array_filter($rows, fn ($r) => $r['level'] !== Wilayah::LEVEL_PROVINSI)),
            count(array_filter($rows, fn ($r) => $r['level'] === Wilayah::LEVEL_KABUPATEN_KOTA)),
            $skipped
        ));

        return self::SUCCESS;
    }

    private function downloadDataset(): ?string
    {
        $target = storage_path('app/wilayah.sql');

        File::ensureDirectoryExists(dirname($target));

        $this->line("Downloading ".self::DATASET_URL.' ...');

        $response = \Illuminate\Support\Facades\Http::timeout(120)->get(self::DATASET_URL);

        if (! $response->successful() || strlen($response->body()) < 1_000_000) {
            $this->error('Dataset download failed. Re-run with --file=/path/to/wilayah.sql');

            return null;
        }

        File::put($target, $response->body());

        return $target;
    }
}
