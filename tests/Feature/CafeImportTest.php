<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Wilayah;
use App\Services\Cafes\AreaAssigner;
use App\Services\Cafes\OverpassClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CafeImportTest extends TestCase
{
    use RefreshDatabase;

    private FakeOverpass $overpass;

    protected function setUp(): void
    {
        parent::setUp();

        Wilayah::create([
            'kode' => '31.74',
            'nama' => 'Kota Jakarta Selatan',
            'level' => Wilayah::LEVEL_KABUPATEN_KOTA,
            'parent_kode' => '31',
        ]);

        $this->overpass = new FakeOverpass;
        $this->app->instance(OverpassClient::class, $this->overpass);
    }

    public function test_it_imports_cafes_and_assigns_the_nearest_area(): void
    {
        $this->overpass->cafes = [
            $this->cafe(1, 'Cafe A', -6.2600, 106.8100, ['opening_hours' => '08:00-22:00', 'addr:street' => 'Jl. A']),
            $this->cafe(2, 'Cafe B', -6.2700, 106.8200),
            $this->cafe(3, '', -6.2800, 106.8300),
        ];
        $this->overpass->areas = [
            $this->area(10, 'Kemang', -6.2601, 106.8101),
            $this->area(11, 'Senopati', -6.2700, 106.8200),
        ];

        $this->artisan('import:cafes', ['region' => '31.74', '--area-id' => '123'])
            ->assertSuccessful();

        $this->assertSame(2, Cafe::count());
        $this->assertSame('Kemang', Cafe::where('osm_id', 1)->value('area_name'));
        $this->assertSame('Senopati', Cafe::where('osm_id', 2)->value('area_name'));
        $this->assertSame('08:00-22:00', Cafe::where('osm_id', 1)->value('opening_hours'));

        $queries = implode("\n", $this->overpass->queries);
        $this->assertStringContainsString('["amenity"="cafe"]', $queries);
        $this->assertStringContainsString('["place"]', $queries);
    }

    public function test_reimport_flags_missing_cafes_without_deleting_them(): void
    {
        $this->overpass->cafes = [
            $this->cafe(1, 'Cafe A', -6.2600, 106.8100),
            $this->cafe(2, 'Cafe B', -6.2700, 106.8200),
        ];

        $this->artisan('import:cafes', ['region' => '31.74', '--area-id' => '123'])->assertSuccessful();

        $this->travel(1)->minute();
        $this->overpass->cafes = [$this->cafe(1, 'Cafe A', -6.2600, 106.8100)];

        $this->artisan('import:cafes', ['region' => '31.74', '--area-id' => '123'])->assertSuccessful();

        $this->assertSame(2, Cafe::count());
        $this->assertFalse(Cafe::where('osm_id', 1)->value('is_missing'));
        $this->assertTrue(Cafe::where('osm_id', 2)->value('is_missing'));
    }

    public function test_haversine_distance_is_in_metres(): void
    {
        $this->assertEqualsWithDelta(111194.9, (new AreaAssigner)->distance(0, 0, 0, 1), 1.0);
    }

    private function cafe(int $id, string $name, float $lat, float $lng, array $tags = []): array
    {
        return [
            'type' => 'node',
            'id' => $id,
            'lat' => $lat,
            'lon' => $lng,
            'tags' => ['name' => $name] + $tags,
            'timestamp' => '2024-01-01T00:00:00Z',
        ];
    }

    private function area(int $id, string $name, float $lat, float $lng): array
    {
        return [
            'type' => 'node',
            'id' => $id,
            'lat' => $lat,
            'lon' => $lng,
            'tags' => ['name' => $name, 'place' => 'suburb'],
        ];
    }
}

class FakeOverpass extends OverpassClient
{
    /** @var list<array<string, mixed>> */
    public array $cafes = [];

    /** @var list<array<string, mixed>> */
    public array $areas = [];

    /** @var list<string> */
    public array $queries = [];

    public function query(string $overpassQl): array
    {
        $this->queries[] = $overpassQl;

        return ['elements' => str_contains($overpassQl, 'amenity') ? $this->cafes : $this->areas];
    }
}
