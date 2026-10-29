<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CafeMapController extends Controller
{
    /**
     * Cafés inside the current viewport, for the /cafes map.
     *
     * Query is a bounding box (no PostGIS needed; works on SQLite tests)
     * plus an optional region filter — panning re-queries locally, so
     * auto-refresh on moveend costs nothing per pan.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bounds' => ['required', 'string', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'region' => ['sometimes', 'string', 'max:10'],
        ]);

        [$south, $west, $north, $east] = array_map('floatval', explode(',', $validated['bounds']));

        $cafes = Cafe::query()
            ->visible()
            ->inBounds($south, $west, $north, $east)
            ->when($validated['region'] ?? null, fn ($q, $region) => $q->where('region_kode', $region))
            ->orderBy('name')
            ->limit((int) config('cafes.api_row_limit', 500))
            ->get([
                'osm_id', 'osm_type', 'name', 'lat', 'lng',
                'area_name', 'opening_hours', 'addr_street', 'addr_housenumber',
                'addr_city', 'osm_timestamp',
            ]);

        return response()->json([
            'data' => $cafes->map(fn (Cafe $cafe) => [
                'id' => $cafe->osm_type.$cafe->osm_id,
                'name' => $cafe->name,
                'lat' => $cafe->lat,
                'lng' => $cafe->lng,
                'area' => $cafe->area_name,
                'hours' => $cafe->opening_hours,
                'address' => $cafe->addressLine(),
                'updated' => $cafe->osm_timestamp?->toDateString(),
            ]),
        ]);
    }
}
