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
        $this->get('/beans')->assertOk();
        $this->get('/beans?category='.$category->slug)->assertOk();
        $this->get('/beans/'.$bean->slug)->assertOk();
        $this->get('/recipes')->assertOk();
        $this->get('/recipes/'.$recipe->slug)->assertOk();
        $this->get('/methods')->assertOk();
        $this->get('/methods/'.$method->slug)->assertOk();
        $this->getJson('/api/recipes/random')->assertOk();
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
