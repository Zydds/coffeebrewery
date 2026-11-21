<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A cafe from OpenStreetMap or an admin-maintained manual record.
 *
 * OSM rows are upserted by (osm_id, osm_type) and flagged when absent from a
 * later run. Manual rows have no OSM identity and are never touched by OSM imports.
 */
class Cafe extends Model
{
    protected $fillable = [
        'source',
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
            'source' => 'string',
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
