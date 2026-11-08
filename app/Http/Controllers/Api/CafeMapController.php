<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CafeMapRequest;
use App\Http\Resources\CafeResource;
use App\Models\Cafe;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CafeMapController extends Controller
{
    /**
     * Cafés inside the current viewport, for the /cafes map.
     *
     * Query is a bounding box (no PostGIS needed; works on SQLite tests)
     * plus an optional region filter — panning re-queries locally, so
     * auto-refresh on moveend costs nothing per pan.
     */
    public function index(CafeMapRequest $request): AnonymousResourceCollection
    {
        [$south, $west, $north, $east] = $request->bounds();

        $cafes = Cafe::query()
            ->visible()
            ->inBounds($south, $west, $north, $east)
            ->when($request->validated('region'), fn ($q, $region) => $q->where('region_kode', $region))
            ->orderBy('name')
            ->limit((int) config('cafes.api_row_limit', 500))
            ->get([
                'osm_id', 'osm_type', 'name', 'lat', 'lng',
                'area_name', 'opening_hours', 'addr_street', 'addr_housenumber',
                'addr_city', 'osm_timestamp',
            ]);

        return CafeResource::collection($cafes);
    }
}
