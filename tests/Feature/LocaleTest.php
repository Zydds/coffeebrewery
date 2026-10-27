<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_english_by_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Bean Catalog')
            ->assertSee('Search beans');
    }

    public function test_locale_switch_route_stores_indonesian_in_session(): void
    {
        $this->get('/locale/id')
            ->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('Bean Catalog')
            ->assertSee('Beranda');
    }

    public function test_locale_switch_back_to_english(): void
    {
        $this->withSession(['locale' => 'id'])
            ->get('/locale/en')
            ->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('Bean Catalog');
    }

    public function test_locale_switch_respects_referer(): void
    {
        $this->get('/locale/id', ['Referer' => 'http://localhost/recipes'])
            ->assertRedirect('/recipes');

        $this->get('/recipes')
            ->assertOk()
            ->assertSee('Resep');
    }

    public function test_invalid_locale_returns_404(): void
    {
        $this->get('/locale/fr')->assertNotFound();
    }

    public function test_indonesian_covers_wizard_and_cheap_pages(): void
    {
        $this->withSession(['locale' => 'id'])
            ->get('/custom')
            ->assertOk()
            ->assertSee('Find Your Brew')
            ->assertSee('Lanjut')
            ->assertSee('Santai saja');

        $this->withSession(['locale' => 'id'])
            ->get('/cheap')
            ->assertOk()
            ->assertSee('Hemat')
            ->assertSee('Brew Methods')
            ->assertSee('Sachet & Instant Coffee');
    }
}
