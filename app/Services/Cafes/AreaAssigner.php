<?php

namespace App\Services\Cafes;

use App\Models\Cafe;
use App\Models\CafeArea;
use App\Models\Wilayah;

/**
 * Assigns each café to its nearest imported place node within the configured
 * radius. Haversine in PHP — a region is a few hundred cafés times a few
 * hundred place nodes, which is well under a second.
 */
class AreaAssigner
{
    public function assign(Wilayah $region): int
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
                        $d = $this->distance($cafe->lat, $cafe->lng, $area->lat, $area->lng);

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

        return $assigned;
    }

    public function distance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(sqrt($a));
    }
}
