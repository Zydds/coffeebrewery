<?php

namespace Tests\Feature;

use App\Models\Cafe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CafeMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_visible_cafes_inside_the_bounds(): void
    {
        $this->cafe(1, 'Inside', -6.2, 106.8);
        $this->cafe(2, 'Outside', -7.2, 110.8);
        $this->cafe(3, 'Missing', -6.21, 106.81, ['is_missing' => true]);

        $response = $this->getJson('/api/cafes?bounds=-6.3,106.7,-6.0,106.9')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        // Exact key set: resources/js/cafes.js reads these names directly.
        $this->assertSame(
            ['id', 'name', 'lat', 'lng', 'area', 'hours', 'address', 'updated'],
            array_keys($response->json('data.0'))
        );

        $this->assertSame('node1', $response->json('data.0.id'));
        $this->assertSame('Inside', $response->json('data.0.name'));
        $this->assertSame('Kemang', $response->json('data.0.area'));
        $this->assertSame('Jl. Kemang 5, Jakarta', $response->json('data.0.address'));
        $this->assertSame('2024-01-01', $response->json('data.0.updated'));
    }

    public function test_it_filters_by_region(): void
    {
        $this->cafe(1, 'Jakarta Cafe', -6.2, 106.8, ['region_kode' => '31.74']);
        $this->cafe(2, 'Bandung Cafe', -6.21, 106.81, ['region_kode' => '32.73']);

        $response = $this->getJson('/api/cafes?bounds=-6.3,106.7,-6.0,106.9&region=32.73')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->assertSame('Bandung Cafe', $response->json('data.0.name'));
    }

    public function test_bounds_are_required_and_validated(): void
    {
        $this->getJson('/api/cafes')->assertUnprocessable();
        $this->getJson('/api/cafes?bounds=not-a-box')->assertUnprocessable();
    }

    private function cafe(int $id, string $name, float $lat, float $lng, array $overrides = []): Cafe
    {
        return Cafe::create($overrides + [
            'osm_id' => $id,
            'osm_type' => 'node',
            'name' => $name,
            'lat' => $lat,
            'lng' => $lng,
            'region_kode' => '31.74',
            'area_name' => 'Kemang',
            'opening_hours' => '08:00-22:00',
            'addr_street' => 'Jl. Kemang',
            'addr_housenumber' => '5',
            'addr_city' => 'Jakarta',
            'osm_timestamp' => '2024-01-01 00:00:00',
        ]);
    }
}
