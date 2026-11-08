<?php

namespace Tests\Unit;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use App\Services\RecipeMatcher;
use Tests\TestCase;

class RecipeMatcherTest extends TestCase
{
    private RecipeMatcher $matcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matcher = new RecipeMatcher;
    }

    public function test_it_scores_sachet_and_bean_methods_against_the_ingredients_answer(): void
    {
        $sachet = $this->recipe('Sachet', 'sachet-kapal-api');
        $v60 = $this->recipe('V60', 'v60-pour-over');

        $this->assertSame(40, $this->matcher->score($sachet, ['ingredients' => 'sachet']));
        $this->assertSame(-60, $this->matcher->score($v60, ['ingredients' => 'sachet']));
        $this->assertSame(25, $this->matcher->score($v60, ['ingredients' => 'beans']));
    }

    public function test_it_rewards_matching_gear_and_penalises_the_rest(): void
    {
        $v60 = $this->recipe('V60', 'v60-pour-over');
        $press = $this->recipe('Press', 'french-press');

        $this->assertSame(65, $this->matcher->score($v60, ['ingredients' => 'beans', 'gear' => 'v60']));
        $this->assertSame(0, $this->matcher->score($press, ['ingredients' => 'beans', 'gear' => 'v60']));
    }

    public function test_it_scores_time_taste_and_drink_preferences(): void
    {
        $quick = $this->recipe('Quick', 'v60-pour-over', brewTime: 3, notes: ['Bright Citrus'], acidity: 5);
        $slow = $this->recipe('Slow', 'v60-pour-over', brewTime: 8);
        $milky = $this->recipe('Susu Hangat', 'v60-pour-over');

        $this->assertSame(55, $this->matcher->score($quick, ['ingredients' => 'beans', 'time' => 'quick']));
        $this->assertSame(-15, $this->matcher->score($slow, ['ingredients' => 'beans', 'time' => 'quick']));
        $this->assertSame(51, $this->matcher->score($quick, ['ingredients' => 'beans', 'taste' => 'fruity']));
        $this->assertSame(50, $this->matcher->score($milky, ['ingredients' => 'beans', 'drink' => 'milk']));
        $this->assertSame(10, $this->matcher->score($milky, ['ingredients' => 'beans', 'drink' => 'black']));
    }

    public function test_it_returns_the_highest_scores_up_to_the_limit(): void
    {
        $recipes = collect([
            $this->recipe('Low', 'v60-pour-over', brewTime: 10),
            $this->recipe('High', 'sachet-kapal-api'),
            $this->recipe('Mid', 'sachet-abc'),
        ]);

        $ranked = $this->matcher->rank($recipes, ['ingredients' => 'sachet'], 2);

        $this->assertSame(['High', 'Mid'], $ranked->pluck('recipe.title')->all());
        $this->assertSame([40, 40], $ranked->pluck('score')->all());
    }

    private function recipe(
        string $title,
        string $methodSlug,
        int $brewTime = 5,
        array $notes = [],
        int $acidity = 3,
    ): Recipe {
        $bean = new CoffeeBean([
            'name' => $title.' Bean',
            'flavor_notes' => $notes,
            'body_level' => 3,
            'acidity_level' => $acidity,
        ]);
        $bean->setRelation('category', new Category(['name' => 'Arabica']));

        $recipe = new Recipe([
            'title' => $title,
            'brew_time_min' => $brewTime,
            'ratio' => '1:15',
        ]);
        $recipe->setRelation('coffeeBean', $bean);
        $recipe->setRelation('brewMethod', new BrewMethod(['name' => $methodSlug, 'slug' => $methodSlug]));

        return $recipe;
    }
}
