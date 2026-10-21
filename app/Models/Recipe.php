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
        'step_by_step',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'step_by_step' => 'array',
            'is_featured' => 'boolean',
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
