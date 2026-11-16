<?php

namespace Tests\Feature\Admin;

use App\Models\CoffeeBean;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    private const BEAN_HEADERS = ['slug', 'name', 'category_slug', 'origin_country', 'acidity_level', 'body_level', 'price', 'price_unit', 'is_cheap', 'flavor_notes', 'recommended_drinks', 'recommended_methods', 'image_url'];

    private const RECIPE_HEADERS = ['slug', 'title', 'bean_slug', 'method_slug', 'water_temp_c', 'grind_size', 'ratio', 'brew_time_min', 'step_by_step', 'is_featured', 'cheap', 'image_url'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_a_valid_bean_file_previews_then_commits(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/import')
            ->assertOk()
            ->assertSee('Bean template');

        $file = $this->csv('beans.csv', self::BEAN_HEADERS, [
            ['test-bean', 'Test Bean', 'arabica', 'Indonesia', '3', '4', '90000', '100 gr', '0', 'Sweet|Nutty', 'Latte', 'v60-pour-over', 'https://cdn.test/bean.jpg'],
            ['test-bean-two', 'Test Bean Two', 'robusta', 'Indonesia', '2', '5', '', '', '', 'Bold', '', '', ''],
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/import/preview', ['type' => 'beans', 'file' => $file])
            ->assertOk()
            ->assertSee('test-bean')
            ->assertViewHas('preview', fn (array $preview) => $preview['valid'] === 2 && $preview['errors'] === 0);

        $this->assertNull(CoffeeBean::where('slug', 'test-bean')->first());

        $this->actingAs($this->admin())
            ->post('/admin/import/commit')
            ->assertOk()
            ->assertViewHas('result', fn (array $result) => $result['created'] === 2 && $result['skipped'] === 0);

        $bean = CoffeeBean::where('slug', 'test-bean')->firstOrFail();

        $this->assertSame(['Sweet', 'Nutty'], $bean->flavor_notes);
        $this->assertSame(['v60-pour-over'], $bean->recommended_methods);
        $this->assertSame('https://cdn.test/bean.jpg', $bean->image_url);
        $this->assertSame('100 gr', CoffeeBean::where('slug', 'test-bean-two')->firstOrFail()->price_unit);
    }

    public function test_bad_rows_are_listed_and_never_written(): void
    {
        $file = $this->csv('beans.csv', self::BEAN_HEADERS, [
            ['', '', 'arabica', 'Indonesia', '3', '3', '', '', '', 'Sweet', '', '', ''],
            ['bad-acid', 'Bad Acid', 'arabica', 'Indonesia', '9', '3', '', '', '', 'Sweet', '', '', ''],
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/import/preview', ['type' => 'beans', 'file' => $file])
            ->assertOk()
            ->assertViewHas('preview', fn (array $preview) => $preview['valid'] === 0
                && $preview['errors'] === 2
                && $preview['rows'][1]['reason'] !== null);

        $this->actingAs($this->admin())
            ->post('/admin/import/commit')
            ->assertOk()
            ->assertViewHas('result', fn (array $result) => $result['created'] === 0
                && $result['skipped'] === 2
                && $result['failed'][1]['key'] === 'bad-acid');

        $this->assertDatabaseMissing('coffee_beans', ['slug' => 'bad-acid']);

        $content = $this->actingAs($this->admin())
            ->get('/admin/import/failed')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->streamedContent();

        $this->assertStringContainsString('bad-acid', $content);
        $this->assertStringContainsString('The acidity level field', $content);
    }

    public function test_an_existing_slug_is_updated_instead_of_duplicated(): void
    {
        $bean = CoffeeBean::firstOrFail();
        $count = CoffeeBean::count();

        $file = $this->csv('beans.csv', self::BEAN_HEADERS, [
            [$bean->slug, 'Sidama Guji', 'arabica', 'Ethiopia', '5', '2', '99000', '100 gr', '1', 'Citrus', '', '', ''],
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/import/preview', ['type' => 'beans', 'file' => $file])
            ->assertOk()
            ->assertViewHas('preview', fn (array $preview) => $preview['updates'] === 1 && $preview['new'] === 0);

        $this->actingAs($this->admin())
            ->post('/admin/import/commit')
            ->assertOk()
            ->assertViewHas('result', fn (array $result) => $result['updated'] === 1 && $result['created'] === 0);

        $this->assertSame($count, CoffeeBean::count());
        $this->assertSame('99000.00', $bean->fresh()->price);
        $this->assertTrue($bean->fresh()->is_cheap);
    }

    public function test_a_recipe_row_fails_when_a_slug_does_not_exist(): void
    {
        $count = Recipe::count();

        $file = $this->csv('recipes.csv', self::RECIPE_HEADERS, [
            ['missing-bean-recipe', 'Missing Bean Recipe', 'no-such-bean', 'v60-pour-over', '93', 'Medium', '1:15', '4', 'Grind|Pour', '0', '0', ''],
            ['missing-method-recipe', 'Missing Method Recipe', 'sidama-guji', 'no-such-method', '93', 'Medium', '1:15', '4', 'Grind|Pour', '0', '0', ''],
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/import/preview', ['type' => 'recipes', 'file' => $file])
            ->assertOk()
            ->assertSee('Bean no-such-bean does not exist.')
            ->assertSee('Method no-such-method does not exist.')
            ->assertViewHas('preview', fn (array $preview) => $preview['errors'] === 2);

        $this->actingAs($this->admin())
            ->post('/admin/import/commit')
            ->assertOk()
            ->assertViewHas('result', fn (array $result) => $result['skipped'] === 2);

        $this->assertSame($count, Recipe::count());
    }

    public function test_oversized_files_are_rejected(): void
    {
        $rows = [];

        for ($i = 1; $i <= 501; $i++) {
            $rows[] = ['bean-'.$i, 'Bean '.$i, 'arabica', 'Indonesia', '3', '3', '', '', '', 'Sweet', '', '', ''];
        }

        $this->actingAs($this->admin())
            ->post('/admin/import/preview', ['type' => 'beans', 'file' => $this->csv('beans.csv', self::BEAN_HEADERS, $rows)])
            ->assertSessionHasErrors('file');

        $big = UploadedFile::fake()->createWithContent('beans.csv', str_repeat('a', 1_100_000));

        $this->actingAs($this->admin())
            ->post('/admin/import/preview', ['type' => 'beans', 'file' => $big])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('coffee_beans', ['slug' => 'bean-1']);
    }

    public function test_a_semicolon_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'beans.csv',
            "name;origin_country;acidity_level\nSemicolon Bean;Indonesia;3\n"
        );

        $this->actingAs($this->admin())
            ->post('/admin/import/preview', ['type' => 'beans', 'file' => $file])
            ->assertSessionHasErrors('file');
    }

    public function test_a_non_admin_cannot_reach_the_importer(): void
    {
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get('/admin/import')->assertForbidden();
        $this->actingAs($member)->post('/admin/import/preview', [])->assertForbidden();
        $this->actingAs($member)->post('/admin/import/commit')->assertForbidden();
    }

    private function admin(): User
    {
        return User::where('is_admin', true)->firstOrFail();
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<string>>  $rows
     */
    private function csv(string $name, array $headers, array $rows): UploadedFile
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers, ',', '"', '');

        foreach ($rows as $row) {
            fputcsv($handle, $row, ',', '"', '');
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return UploadedFile::fake()->createWithContent($name, $contents);
    }
}
