<?php

namespace Tests\Feature;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * JSON routes have no session, so the locale must travel as a query
 * parameter. Without it the random endpoint would always answer in English.
 */
class ApiLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_random_endpoint_translates_steps_from_the_locale_query_parameter(): void
    {
        $this->seedRecipe();

        $this->getJson('/api/recipes/random?locale=id')
            ->assertOk()
            ->assertJsonPath('data.step_by_step.0', 'Tekan perlahan lalu tuang.');

        $this->getJson('/api/recipes/random?locale=en')
            ->assertOk()
            ->assertJsonPath('data.step_by_step.0', 'Press down slowly and pour.');
    }

    public function test_random_endpoint_falls_back_to_english_without_a_locale(): void
    {
        $this->seedRecipe();

        $this->getJson('/api/recipes/random')
            ->assertOk()
            ->assertJsonPath('data.step_by_step.0', 'Press down slowly and pour.');
    }

    public function test_regional_locale_suffixes_resolve_to_the_base_language(): void
    {
        $this->seedRecipe();

        $this->getJson('/api/recipes/random?locale=id-ID')
            ->assertOk()
            ->assertJsonPath('data.step_by_step.0', 'Tekan perlahan lalu tuang.');
    }

    public function test_accept_language_is_the_fallback_for_stateless_requests(): void
    {
        $this->seedRecipe();

        $this->withHeader('Accept-Language', 'id-ID,id;q=0.9')
            ->getJson('/api/recipes/random')
            ->assertOk()
            ->assertJsonPath('data.step_by_step.0', 'Tekan perlahan lalu tuang.');

        $this->withHeader('Accept-Language', 'id')
            ->getJson('/api/recipes/random?locale=en')
            ->assertOk()
            ->assertJsonPath('data.step_by_step.0', 'Press down slowly and pour.');
    }

    private function seedRecipe(): void
    {
        $category = Category::create(['name' => 'Arabica', 'slug' => 'arabica']);

        $bean = CoffeeBean::create([
            'category_id' => $category->id,
            'name' => 'Gayo Highland',
            'slug' => 'gayo-highland',
            'origin_country' => 'Indonesia',
            'acidity_level' => 3,
            'body_level' => 4,
            'flavor_notes' => ['Chocolate'],
            'recommended_drinks' => [],
        ]);

        $method = BrewMethod::create([
            'name' => 'French Press',
            'slug' => 'french-press',
            'difficulty_level' => 'easy',
            'description' => 'Immersion brewing.',
            'equipment_needed' => ['French press'],
        ]);

        Recipe::create([
            'coffee_bean_id' => $bean->id,
            'brew_method_id' => $method->id,
            'title' => 'Classic French Press',
            'slug' => 'classic-french-press',
            'water_temp_c' => 93,
            'grind_size' => 'Coarse',
            'ratio' => '1:15',
            'step_by_step' => ['Press down slowly and pour.'],
        ]);
    }
}
