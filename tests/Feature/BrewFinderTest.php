<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrewFinderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_custom_page_renders(): void
    {
        $this->get('/custom')
            ->assertOk()
            ->assertSee('What coffee do you have?')
            ->assertSee('How much time do you have?')
            ->assertSee('Find Your Brew');
    }

    public function test_suggest_api_returns_quick_sachet_match(): void
    {
        $response = $this->getJson('/api/recipes/suggest?ingredients=sachet&time=quick&gear=mug&taste=strong&drink=black');

        $response->assertOk();
        $first = $response->json('data.0');

        $this->assertTrue($first['cheap']);
        $this->assertLessThanOrEqual(5, $first['brew_time_min']);
    }

    public function test_suggest_api_prefers_matching_gear(): void
    {
        $response = $this->getJson('/api/recipes/suggest?ingredients=beans&time=rush&gear=v60&taste=fruity&drink=black');

        $response->assertOk();
        $this->assertSame('V60 Pour Over', $response->json('data.0.method.name'));
    }

    public function test_suggest_api_validates_input(): void
    {
        $this->getJson('/api/recipes/suggest')->assertStatus(422);
        $this->getJson('/api/recipes/suggest?ingredients=tea')->assertStatus(422);
    }
}
