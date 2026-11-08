<?php

namespace App\Support;

use App\Models\Recipe;

/**
 * Everything the recipe detail page needs for search engines and social
 * previews: the meta description, the share image, the schema.org Recipe
 * JSON-LD, and the serving scale.
 *
 * Kept out of Blade so the JSON-LD can be asserted as data, and so
 * scaleInfo() runs once per request instead of twice.
 */
class RecipeSeo
{
    /** @var array{coffee_g: int, water_ml: int}|null */
    private ?array $scale;

    public function __construct(
        private Recipe $recipe
    ) {
        $this->scale = $recipe->scaleInfo();
    }

    public function description(): string
    {
        return __('Brew :method with :bean — :ratio ratio, :temp°C water, ready in :time minutes. Step-by-step guide with grind size and servings.', [
            'method' => $this->recipe->brewMethod?->name ?? __('your brew method'),
            'bean' => $this->recipe->coffeeBean?->name ?? __('your beans'),
            'ratio' => $this->recipe->ratio,
            'temp' => $this->recipe->water_temp_c,
            'time' => $this->recipe->brew_time_min ?? __('a few'),
        ]);
    }

    public function image(): ?string
    {
        return $this->recipe->coffeeBean?->image_url;
    }

    /**
     * @return array{coffee_g: int, water_ml: int}|null
     */
    public function scale(): ?array
    {
        return $this->scale;
    }

    /**
     * schema.org/Recipe — eligible for Google's Recipe rich results.
     * Instructions and yield keep the same __() translation the page
     * body applies to the stored-English step texts.
     */
    public function jsonLd(): string
    {
        $recipe = $this->recipe;
        $scale = $this->scale;

        $data = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => $recipe->title,
            'description' => html_entity_decode(trim($this->description()), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'url' => route('recipes.show', $recipe->slug),
            'image' => $recipe->coffeeBean?->image_url,
            'author' => ['@type' => 'Organization', 'name' => config('app.name', 'Coffeebrewery')],
            'totalTime' => $recipe->brew_time_min ? 'PT'.$recipe->brew_time_min.'M' : null,
            'recipeYield' => __('1 serving'),
            'recipeIngredient' => $scale
                ? [$scale['coffee_g'].' '.__('g coffee beans'), $scale['water_ml'].' '.__('ml water')]
                : [__('Coffee beans'), __('Water')],
            'recipeInstructions' => collect($recipe->step_by_step ?? [])->map(fn ($step, $i) => [
                '@type' => 'HowToStep',
                'position' => $i + 1,
                'text' => __($step),
            ])->values()->all(),
            'recipeCategory' => $recipe->coffeeBean?->category?->name,
            'keywords' => trim(implode(', ', array_filter([
                $recipe->coffeeBean?->name,
                $recipe->brewMethod?->name,
            ]))) ?: null,
            'tool' => $recipe->brewMethod
                ? ['@type' => 'HowToTool', 'name' => $recipe->brewMethod->name]
                : null,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    }
}
