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
        'image_url',
        'image_public_id',
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

    public function scopeQuick($query)
    {
        return $query->whereNotNull('brew_time_min')->where('brew_time_min', '<=', 5);
    }

    public function scopeCheap($query)
    {
        return $query->where('cheap', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $needle = '%'.mb_strtolower($term).'%';

        return $query->where(fn ($w) => $w
            ->whereRaw('LOWER(title) LIKE ?', [$needle])
            ->orWhereHas('coffeeBean', fn ($b) => $b->whereRaw('LOWER(name) LIKE ?', [$needle]))
            ->orWhereHas('brewMethod', fn ($m) => $m->whereRaw('LOWER(name) LIKE ?', [$needle])));
    }

    public function scopeForMethod($query, ?string $slug)
    {
        return $slug
            ? $query->whereHas('brewMethod', fn ($q) => $q->where('slug', $slug))
            : $query;
    }

    /**
     * Shared random-pick constraints for the web redirect and the JSON
     * endpoint, so both surfaces accept the same cheap/quick/exclude rule.
     */
    public function scopeForPicker($query, bool $cheap = false, bool $quick = false, int $exclude = 0)
    {
        return $query
            ->when($exclude, fn ($q) => $q->whereKeyNot($exclude))
            ->when($cheap, fn ($q) => $q->cheap())
            ->when($quick, fn ($q) => $q->quick());
    }

    /**
     * The JSON fields shared by the random and suggest endpoints. Callers
     * append their own fields (step_by_step, cheap, score) so the keys stay
     * compatible with resources/js/app.js.
     */
    public function summary(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'ratio' => $this->ratio,
            'brew_time_min' => $this->brew_time_min,
            'water_temp' => $this->water_temp_c.'°C',
            'grind_size' => $this->grind_size,
            'bean' => [
                'name' => $this->coffeeBean?->name,
                'category' => $this->coffeeBean?->category?->name,
            ],
            'method' => [
                'name' => $this->brewMethod?->name,
            ],
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
