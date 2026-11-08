<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards CafeMapPage: the /cafes cascade, counts, and the bootstrap that
 * cafes.js reads. A 200 alone would not catch wrong counts or bounds.
 */
class CafePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_counts_and_bounds_for_the_default_region(): void
    {
        $this->seedWilayah();

        $this->cafe(1, 'South Jakarta A', -6.26, 106.81, '31.74');
        $this->cafe(2, 'South Jakarta B', -6.24, 106.83, '31.74');
        $this->cafe(3, 'Bandung', -6.90, 107.60, '32.73');

        $response = $this->get('/cafes')->assertOk();

        $this->assertSame('31.74', $response->viewData('region')->kode);
        $this->assertSame('31.74', $response->viewData('defaultRegion'));

        $bootstrap = $response->viewData('bootstrap');

        $this->assertSame('31.74', $bootstrap['region']);
        $this->assertSame(route('api.cafes.index'), $bootstrap['apiUrl']);
        $this->assertSame(2, (int) $bootstrap['counts']['31.74']);
        $this->assertSame(1, (int) $bootstrap['counts']['32.73']);

        $southJakarta = $bootstrap['bboxes']['31.74'];
        $this->assertEqualsWithDelta(-6.26, $southJakarta['s'], 0.00001);
        $this->assertEqualsWithDelta(-6.24, $southJakarta['n'], 0.00001);
        $this->assertEqualsWithDelta(106.81, $southJakarta['w'], 0.00001);
        $this->assertEqualsWithDelta(106.83, $southJakarta['e'], 0.00001);

        $this->assertArrayHasKey('loading', $bootstrap['i18n']);
        $this->assertArrayHasKey('emptyRegion', $bootstrap['i18n']);
    }

    public function test_an_unknown_region_falls_back_to_the_default(): void
    {
        $this->seedWilayah();

        $response = $this->get('/cafes?region=99.99')->assertOk();

        $this->assertSame('31.74', $response->viewData('region')->kode);
    }

    public function test_a_requested_region_is_selected(): void
    {
        $this->seedWilayah();

        $response = $this->get('/cafes?region=32.73')->assertOk();

        $this->assertSame('32.73', $response->viewData('region')->kode);
        $this->assertSame('32.73', $response->viewData('bootstrap')['region']);
    }

    private function seedWilayah(): void
    {
        Wilayah::create([
            'kode' => '31',
            'nama' => 'DKI Jakarta',
            'level' => Wilayah::LEVEL_PROVINSI,
        ]);

        Wilayah::create([
            'kode' => '31.74',
            'nama' => 'Kota Jakarta Selatan',
            'level' => Wilayah::LEVEL_KABUPATEN_KOTA,
            'parent_kode' => '31',
        ]);

        Wilayah::create([
            'kode' => '32',
            'nama' => 'Jawa Barat',
            'level' => Wilayah::LEVEL_PROVINSI,
        ]);

        Wilayah::create([
            'kode' => '32.73',
            'nama' => 'Kota Bandung',
            'level' => Wilayah::LEVEL_KABUPATEN_KOTA,
            'parent_kode' => '32',
        ]);
    }

    private function cafe(int $id, string $name, float $lat, float $lng, string $region): Cafe
    {
        return Cafe::create([
            'osm_id' => $id,
            'osm_type' => 'node',
            'name' => $name,
            'lat' => $lat,
            'lng' => $lng,
            'region_kode' => $region,
        ]);
    }
}
