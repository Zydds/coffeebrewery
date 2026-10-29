<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * An area (OSM place node: suburb/neighbourhood/village/...) that contains
 * cafés in a region — the in-map area dropdown's backbone.
 *
 * Two row sources:
 *  - imported place nodes (osm_id set, is_alias false)
 *  - curated aliases for famous names OSM is missing entirely (Kemang,
 *    SCBD, ...; osm_id null, is_alias true) — see config/cafe_area_aliases.php
 *
 * The dropdown shows only areas with at least one café (a count query),
 * never a bare list of empty kelurahan.
 */
class CafeArea extends Model
{
    protected $fillable = [
        'osm_id',
        'name',
        'place_type',
        'lat',
        'lng',
        'region_kode',
        'is_alias',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'is_alias' => 'boolean',
        ];
    }
}
