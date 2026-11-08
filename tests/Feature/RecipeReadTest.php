<?php

namespace Tests\Feature;

use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeReadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_quick_scope_limits_to_five_minutes(): void
    {
        $recipes = Recipe::quick()->get();

        $this->assertNotEmpty($recipes);
        $this->assertTrue($recipes->every(
            fn (Recipe $recipe) => $recipe->brew_time_min !== null && $recipe->brew_time_min <= 5
        ));
        $this->assertTrue($recipes->contains('title', 'Quick Double Espresso'));
        $this->assertFalse($recipes->contains('title', 'Easy Morning French Press'));
    }

    public function test_search_scope_matches_title_bean_and_method(): void
    {
        $this->assertTrue(Recipe::search('quick')->get()->contains('title', 'Quick Double Espresso'));
        $this->assertTrue(Recipe::search('sidama')->get()->contains('title', 'Ethiopian Bright Citrus Pour Over'));
        $this->assertTrue(Recipe::search('french press')->get()->isNotEmpty());
        $this->assertSame(Recipe::count(), Recipe::search('   ')->count());
    }

    public function test_summary_returns_the_shared_payload_shape(): void
    {
        $recipe = Recipe::with(['coffeeBean.category', 'brewMethod'])
            ->where('title', 'Quick Double Espresso')
            ->firstOrFail();

        $summary = $recipe->summary();

        $this->assertSame([
            'id',
            'title',
            'slug',
            'ratio',
            'brew_time_min',
            'water_temp',
            'grind_size',
            'bean',
            'method',
        ], array_keys($summary));
        $this->assertSame($recipe->water_temp_c.'°C', $summary['water_temp']);
        $this->assertSame($recipe->coffeeBean->name, $summary['bean']['name']);
        $this->assertSame($recipe->coffeeBean->category->name, $summary['bean']['category']);
        $this->assertSame($recipe->brewMethod->name, $summary['method']['name']);
    }

    public function test_random_endpoint_returns_the_summary_plus_steps(): void
    {
        $data = $this->getJson('/api/recipes/random?quick=1')->assertOk()->json('data');

        $this->assertEqualsCanonicalizing([
            'id',
            'title',
            'slug',
            'ratio',
            'brew_time_min',
            'water_temp',
            'grind_size',
            'bean',
            'method',
            'step_by_step',
        ], array_keys($data));
        $this->assertLessThanOrEqual(5, $data['brew_time_min']);
        $this->assertArrayHasKey('name', $data['bean']);
        $this->assertArrayHasKey('category', $data['bean']);
        $this->assertArrayHasKey('name', $data['method']);
    }

    public function test_suggest_endpoint_returns_the_summary_plus_score_and_cheap(): void
    {
        $data = $this->getJson('/api/recipes/suggest?ingredients=sachet&time=quick&gear=mug&taste=strong&drink=black')
            ->assertOk()
            ->json('data.0');

        $this->assertEqualsCanonicalizing([
            'id',
            'title',
            'slug',
            'ratio',
            'brew_time_min',
            'water_temp',
            'grind_size',
            'bean',
            'method',
            'cheap',
            'score',
        ], array_keys($data));
        $this->assertIsBool($data['cheap']);
        $this->assertIsInt($data['score']);
    }
}
