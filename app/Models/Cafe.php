<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A café imported from OpenStreetMap (amenity=cafe).
 *
 * Data trust chain: OSM import only. No owner claims, no admin CRUD in v1 —
 * corrections happen upstream on OSM and the next import picks them up.
 * Rows are upserted by (osm_id, osm_type) and flagged is_missing when absent
 * from a later run; they are never deleted, so a flaky Overpass response
 * can't silently wipe a region.
 */
class Cafe extends Model
{
    protected $fillable = [
        'osm_id',
        'osm_type',
        'name',
        'lat',
        'lng',
        'region_kode',
        'area_name',
        'opening_hours',
        'addr_street',
        'addr_housenumber',
        'addr_city',
        'tags',
        'osm_timestamp',
        'last_seen_at',
        'is_missing',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'tags' => 'array',
            'osm_timestamp' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_missing' => 'boolean',
        ];
    }

    /**
     * Visible cafés inside a viewport, for the map API.
     * Bounding-box filtering (no PostGIS) keeps this runnable on SQLite tests.
     */
    public function scopeInBounds($query, float $south, float $west, float $north, float $east)
    {
        return $query
            ->whereBetween('lat', [$south, $north])
            ->whereBetween('lng', [$west, $east]);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_missing', false);
    }

    /**
     * Street + number + city line for the card, or null when OSM has nothing.
     * Only ~18% of Jakarta Selatan cafés carry any addr:* tag, so the card
     * renders this conditionally — never a blank "Address: -" row.
     */
    public function addressLine(): ?string
    {
        $street = trim($this->addr_street.' '.($this->addr_housenumber ?? ''));

        $parts = array_filter([
            $street !== '' ? $street : null,
            $this->addr_city,
        ]);

        return $parts ? implode(', ', $parts) : null;
    }
}
