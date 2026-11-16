<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoffeeBean extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'origin_country',
        'acidity_level',
        'body_level',
        'price',
        'price_unit',
        'is_cheap',
        'flavor_notes',
        'recommended_drinks',
        'recommended_methods',
        'image_url',
        'image_public_id',
    ];

    protected function casts(): array
    {
        return [
            'flavor_notes' => 'array',
            'recommended_drinks' => 'array',
            'recommended_methods' => 'array',
            'price' => 'decimal:2',
            'is_cheap' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Sprite symbol for the drawn fallback: packs and instant coffee get
     * the sachet, everything else the bean.
     */
    public function illustration(): string
    {
        return in_array($this->category?->slug, ['sachet', 'instant'], true) ? 'sachet' : 'bean';
    }
}
