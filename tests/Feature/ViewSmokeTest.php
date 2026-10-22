<?php

namespace Tests\Feature;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_all_public_pages_render(): void
    {
        $bean = CoffeeBean::firstOrFail();
        $recipe = Recipe::firstOrFail();
        $method = BrewMethod::firstOrFail();
        $category = Category::firstOrFail();

        $this->get('/')->assertOk();
        $this->get('/?category='.$category->slug)->assertOk();
        $this->get('/?q=java')->assertOk();
        $this->get('/?method_q=press')->assertOk();
        $this->get('/beans/'.$bean->slug)->assertOk();
        $this->get('/recipes')->assertOk();
        $this->get('/recipes?q=press')->assertOk();
        $this->get('/recipes?method='.$method->slug)->assertOk();
        $this->get('/recipes?featured=1')->assertOk();
        $this->get('/recipes/'.$recipe->slug)->assertOk();
        $this->get('/recipes?cheap=1')->assertOk();
        $this->get('/recipes?quick=1')->assertOk();
        $this->get('/recipes/random')->assertRedirect();
        $this->get('/methods/'.$method->slug)->assertOk();
        $this->get('/cheap')->assertOk();
        $this->get('/cheap?q=nescafe')->assertOk();
        $this->get('/cheap?method_q=sachet')->assertOk();
        $this->get('/custom')->assertOk();
        $this->getJson('/api/recipes/random')->assertOk();
        $this->getJson('/api/recipes/random?cheap=1')->assertOk();
        $this->getJson('/api/recipes/random?quick=1')->assertOk();
    }

    public function test_quick_filter_only_shows_quick_recipes(): void
    {
        $this->get('/recipes?quick=1')
            ->assertOk()
            ->assertSee('Quick Double Espresso')
            ->assertDontSee('Easy Morning French Press');
    }

    public function test_random_redirects_to_a_recipe_page(): void
    {
        $response = $this->get('/recipes/random');

        $response->assertRedirect();
        $this->assertMatchesRegularExpression('#/recipes/[^/]+$#', $response->headers->get('Location'));
    }

    public function test_removed_index_pages_return_404(): void
    {
        $this->get('/beans')->assertNotFound();
        $this->get('/methods')->assertNotFound();
    }

    public function test_all_admin_pages_render_for_admin(): void
    {
        $admin = User::where('is_admin', true)->firstOrFail();
        $bean = CoffeeBean::firstOrFail();
        $recipe = Recipe::firstOrFail();
        $method = BrewMethod::firstOrFail();
        $category = Category::firstOrFail();

        $urls = [
            '/admin',
            '/admin/categories',
            '/admin/categories/create',
            '/admin/categories/'.$category->id.'/edit',
            '/admin/beans',
            '/admin/beans/create',
            '/admin/beans/'.$bean->id.'/edit',
            '/admin/methods',
            '/admin/methods/create',
            '/admin/methods/'.$method->id.'/edit',
            '/admin/recipes',
            '/admin/recipes/create',
            '/admin/recipes/'.$recipe->id.'/edit',
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_register_route_returns_404(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }
}
