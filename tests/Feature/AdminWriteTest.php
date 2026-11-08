<?php

namespace Tests\Feature;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use App\Models\User;
use App\Services\CloudinaryUploader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Tests\TestCase;

class AdminWriteTest extends TestCase
{
    use RefreshDatabase;

    private FakeUploader $uploader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->uploader = new FakeUploader;
        $this->app->instance(CloudinaryUploader::class, $this->uploader);
    }

    public function test_it_creates_a_bean_with_a_derived_slug_and_image(): void
    {
        $this->actingAs($this->admin())->post('/admin/beans', [
            'category_id' => Category::firstOrFail()->id,
            'name' => 'Test Bean',
            'origin_country' => 'Indonesia',
            'acidity_level' => 3,
            'body_level' => 4,
            'flavor_notes' => ['Sweet'],
            'recommended_drinks' => [],
            'image' => UploadedFile::fake()->image('bean.jpg'),
        ])->assertRedirect(route('admin.beans.index'));

        $bean = CoffeeBean::where('slug', 'test-bean')->firstOrFail();

        $this->assertSame('coffee-app/beans/bean.jpg', $bean->image_public_id);
        $this->assertSame('coffee-app/beans/bean.jpg', $this->uploader->uploaded);
    }

    public function test_it_stores_an_empty_array_when_recommended_drinks_are_omitted(): void
    {
        $this->actingAs($this->admin())->post('/admin/beans', [
            'category_id' => Category::firstOrFail()->id,
            'name' => 'No Drinks Bean',
            'origin_country' => 'Indonesia',
            'acidity_level' => 3,
            'body_level' => 3,
            'flavor_notes' => ['Sweet'],
        ])->assertRedirect(route('admin.beans.index'));

        $bean = CoffeeBean::where('slug', 'no-drinks-bean')->firstOrFail();

        $this->assertSame([], $bean->recommended_drinks);
    }

    public function test_blank_recommended_drink_rows_are_dropped(): void
    {
        $this->actingAs($this->admin())->post('/admin/beans', [
            'category_id' => Category::firstOrFail()->id,
            'name' => 'One Drink Bean',
            'origin_country' => 'Indonesia',
            'acidity_level' => 3,
            'body_level' => 3,
            'flavor_notes' => ['Sweet'],
            'recommended_drinks' => ['', 'Iced latte'],
        ])->assertRedirect(route('admin.beans.index'));

        $bean = CoffeeBean::where('slug', 'one-drink-bean')->firstOrFail();

        $this->assertSame(['Iced latte'], $bean->recommended_drinks);
    }

    public function test_it_updates_a_bean_and_deletes_the_old_image_after_uploading(): void
    {
        $bean = CoffeeBean::firstOrFail();
        $bean->update(['image_public_id' => 'old/public', 'image_url' => 'https://cdn.test/old.jpg']);

        $this->actingAs($this->admin())->put('/admin/beans/'.$bean->id, [
            'category_id' => $bean->category_id,
            'name' => 'Renamed Bean',
            'origin_country' => $bean->origin_country,
            'acidity_level' => $bean->acidity_level,
            'body_level' => $bean->body_level,
            'flavor_notes' => $bean->flavor_notes,
            'image' => UploadedFile::fake()->image('new.jpg'),
        ])->assertRedirect(route('admin.beans.index'));

        $bean->refresh();

        $this->assertSame('renamed-bean', $bean->slug);
        $this->assertSame('coffee-app/beans/new.jpg', $bean->image_public_id);
        $this->assertSame(['old/public'], $this->uploader->deleted);
    }

    public function test_destroy_keeps_the_row_deleted_when_the_image_delete_fails(): void
    {
        $bean = CoffeeBean::create([
            'category_id' => Category::firstOrFail()->id,
            'name' => 'Disposable Bean',
            'slug' => 'disposable-bean',
            'origin_country' => 'Indonesia',
            'acidity_level' => 3,
            'body_level' => 3,
            'flavor_notes' => [],
            'recommended_drinks' => [],
            'image_public_id' => 'old/public',
        ]);

        $this->app->instance(CloudinaryUploader::class, new class extends FakeUploader
        {
            public function delete(?string $publicId): void
            {
                throw new RuntimeException('CDN down');
            }
        });

        $this->actingAs($this->admin())
            ->delete('/admin/beans/'.$bean->id)
            ->assertRedirect(route('admin.beans.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('coffee_beans', ['id' => $bean->id]);
    }

    public function test_duplicate_slugs_are_rejected_for_every_resource(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Arabica',
        ])->assertSessionHasErrors('name');

        $this->actingAs($admin)->post('/admin/methods', [
            'name' => 'V60 Pour Over',
            'difficulty_level' => 'easy',
            'description' => 'Duplicate method',
            'equipment_needed' => ['Kettle'],
        ])->assertSessionHasErrors('name');

        $this->actingAs($admin)->post('/admin/beans', [
            'category_id' => Category::firstOrFail()->id,
            'name' => 'Sidama Guji',
            'origin_country' => 'Ethiopia',
            'acidity_level' => 4,
            'body_level' => 3,
            'flavor_notes' => ['Citrus'],
        ])->assertSessionHasErrors('name');

        $this->actingAs($admin)->post('/admin/recipes', [
            'coffee_bean_id' => CoffeeBean::firstOrFail()->id,
            'brew_method_id' => BrewMethod::firstOrFail()->id,
            'title' => Recipe::firstOrFail()->title,
            'water_temp_c' => 93,
            'grind_size' => 'Medium',
            'ratio' => '1:15',
            'step_by_step' => ['Brew it.'],
        ])->assertSessionHasErrors('title');
    }

    private function admin(): User
    {
        return User::where('is_admin', true)->firstOrFail();
    }
}

class FakeUploader extends CloudinaryUploader
{
    public ?string $uploaded = null;

    /** @var list<string|null> */
    public array $deleted = [];

    public function upload(UploadedFile $file, string $folder = 'coffee-app'): array
    {
        $this->uploaded = "{$folder}/{$file->getClientOriginalName()}";

        return ['secure_url' => 'https://cdn.test/'.$this->uploaded, 'public_id' => $this->uploaded];
    }

    public function replace(UploadedFile $file, ?string $oldPublicId, string $folder = 'coffee-app'): array
    {
        $this->deleted[] = $oldPublicId;

        return $this->upload($file, $folder);
    }

    public function delete(?string $publicId): void
    {
        $this->deleted[] = $publicId;
    }
}
