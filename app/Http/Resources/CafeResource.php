<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One map pin. The keys are a contract with resources/js/cafes.js — keep
 * them stable, including the string id (osm type + osm id).
 */
class CafeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->osm_type.$this->osm_id,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'area' => $this->area_name,
            'hours' => $this->opening_hours,
            'address' => $this->addressLine(),
            'updated' => $this->osm_timestamp?->toDateString(),
        ];
    }
}
