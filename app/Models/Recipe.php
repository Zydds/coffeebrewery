<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    protected $fillable = [
        'coffee_bean_id',
        'brew_method_id',
        'title',
        'slug',
        'water_temp_c',
        'grind_size',
        'ratio',
        'brew_time_min',
        'step_by_step',
        'is_featured',
        'cheap',
    ];

    protected function casts(): array
    {
        return [
            'step_by_step' => 'array',
            'is_featured' => 'boolean',
            'cheap' => 'boolean',
            'brew_time_min' => 'integer',
        ];
    }

    /**
     * Grams/ml amounts for one serving when the ratio is numeric (1:N).
     * Espresso-style ratios (small N) get an 18g dose; filter/press get 15g.
     */
    public function scaleInfo(): ?array
    {
        if (! preg_match('/^1:(\d+(?:\.\d+)?)$/', trim((string) $this->ratio), $m)) {
            return null;
        }

        $factor = (float) $m[1];
        $coffee = $factor <= 3 ? 18 : 15;

        return [
            'coffee_g' => $coffee,
            'water_ml' => (int) round($coffee * $factor),
        ];
    }

    public function coffeeBean(): BelongsTo
    {
        return $this->belongsTo(CoffeeBean::class);
    }

    public function brewMethod(): BelongsTo
    {
        return $this->belongsTo(BrewMethod::class);
    }
}
