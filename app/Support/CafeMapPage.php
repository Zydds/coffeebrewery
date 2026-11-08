<?php

namespace App\Support;

use App\Models\Cafe;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Server-side data for the /cafes page: the province → city cascade, café
 * counts, per-region bounds, and the JSON bootstrap read by cafes.js.
 *
 * Kept as one small presenter instead of a repository because the two
 * aggregate queries only serve this page.
 */
class CafeMapPage
{
    /**
     * @return array{
     *     region: ?Wilayah,
     *     provinsi: Collection,
     *     kabupatenKota: Collection,
     *     counts: Collection,
     *     defaultRegion: string,
     *     bootstrap: array<string, mixed>
     * }
     */
    public function data(Request $request): array
    {
        $defaultRegion = (string) config('cafes.default_region', '31.74');

        $region = Wilayah::kabupatenKota()
            ->where('kode', $request->query('region', $defaultRegion))
            ->first()
            ?? Wilayah::kabupatenKota()->where('kode', $defaultRegion)->first();

        // One grouped query gives every region's café count — the cascade's
        // availability badges come from this, not per-row subqueries.
        $counts = Cafe::visible()
            ->groupBy('region_kode')
            ->selectRaw('region_kode, count(*) as cafe_count')
            ->pluck('cafe_count', 'region_kode');

        $provinsi = Wilayah::provinsi()->orderBy('nama')->get();

        $kabupatenKota = Wilayah::kabupatenKota()
            ->orderBy('nama')
            ->get()
            ->groupBy('parent_kode');

        // Bounding boxes per region with cafés, so the JS map can fit a
        // region without an extra round-trip. Built here (not inline in
        // the view) — @json can't parse multi-line closures in a blade
        // directive expression.
        $bboxes = Cafe::visible()
            ->groupBy('region_kode')
            ->selectRaw('region_kode, min(lat) as s, max(lat) as n, min(lng) as w, max(lng) as e')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->region_kode => [
                's' => round((float) $row->s, 5),
                'n' => round((float) $row->n, 5),
                'w' => round((float) $row->w, 5),
                'e' => round((float) $row->e, 5),
            ]]);

        return [
            'region' => $region,
            'provinsi' => $provinsi,
            'kabupatenKota' => $kabupatenKota,
            'counts' => $counts,
            'defaultRegion' => $defaultRegion,
            'bootstrap' => [
                'apiUrl' => route('api.cafes.index'),
                'region' => $region?->kode,
                'defaultRegion' => $defaultRegion,
                'bboxes' => $bboxes,
                'counts' => $counts,
                'i18n' => [
                    'loading' => __('Loading cafes…'),
                    'emptyRegion' => __('No cafes in this region yet.'),
                    'loadError' => __('Could not load cafes. Try again.'),
                    'directions' => __('Get directions'),
                    'updated' => __('Data last updated'),
                    'hours' => __('Hours'),
                    'countLabel' => __('cafes in this city'),
                ],
            ],
        ];
    }
}
