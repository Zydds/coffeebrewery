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
        'image_url',
        'image_public_id',
    ];

    protected function casts(): array
    {
        return [
            'flavor_notes' => 'array',
            'recommended_drinks' => 'array',
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
}
