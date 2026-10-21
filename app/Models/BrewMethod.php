<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrewMethod extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'difficulty_level',
        'description',
        'equipment_needed',
        'image_url',
        'image_public_id',
    ];

    protected function casts(): array
    {
        return [
            'equipment_needed' => 'array',
        ];
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
