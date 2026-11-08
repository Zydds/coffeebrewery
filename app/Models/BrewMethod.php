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
        'is_cheap',
        'equipment_needed',
        'image_url',
        'image_public_id',
    ];

    protected function casts(): array
    {
        return [
            'equipment_needed' => 'array',
            'is_cheap' => 'boolean',
        ];
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Sprite symbol for the drawn fallback. Unknown methods fall back to
     * the bean rather than shipping an empty card.
     */
    public function illustration(): string
    {
        return match (true) {
            $this->slug === 'es-kopi-sachet' => 'iced',
            str_contains((string) $this->slug, 'sachet') => 'sachet',
            default => [
                'v60-pour-over' => 'v60',
                'french-press' => 'french-press',
                'aeropress' => 'aeropress',
                'espresso-machine' => 'espresso',
            ][$this->slug] ?? 'bean',
        };
    }
}
