<?php

namespace Tests\Feature;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A category, bean, method, and recipe so every public page (incl. the
     * JSON-LD on the recipe detail) has data to describe.
     *
     * @return array{bean: CoffeeBean, method: BrewMethod, recipe: Recipe}
     */
    private function brewWorld(bool $withImage = false): array
    {
        $category = Category::create([
            'name' => 'Single Origin',
            'slug' => 'single-origin',
        ]);

        $bean = CoffeeBean::create([
            'category_id' => $category->id,
            'name' => 'Gayo Highland',
            'slug' => 'gayo-highland',
            'origin_country' => 'Indonesia',
            'acidity_level' => 3,
            'body_level' => 4,
            'flavor_notes' => ['earthy', 'herbal'],
            'recommended_drinks' => ['pour over'],
            'image_url' => $withImage
                ? 'https://res.cloudinary.com/demo/image/upload/beans.jpg'
                : null,
        ]);

        $method = BrewMethod::create([
            'name' => 'V60 Pour Over',
            'slug' => 'v60-pour-over',
            'difficulty_level' => 'medium',
            'description' => 'A classic pour-over method using a V60 dripper.',
            'equipment_needed' => ['V60 dripper', 'kettle'],
        ]);

        $recipe = Recipe::create([
            'coffee_bean_id' => $bean->id,
            'brew_method_id' => $method->id,
            'title' => 'Gayo V60 Morning Brew',
            'slug' => 'gayo-v60-morning-brew',
            'water_temp_c' => 92,
            'grind_size' => 'medium-fine',
            'ratio' => '1:15',
            'brew_time_min' => 4,
            'step_by_step' => ['Rinse the filter.', 'Bloom for 30 seconds.', 'Pour and serve.'],
        ]);

        return compact('bean', 'method', 'recipe');
    }

    private function jsonLd(TestResponse $response): array
    {
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $m);

        $this->assertNotEmpty($m, 'No JSON-LD block found on the page');

        return json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);
    }

    public function test_home_page_uses_the_site_wide_description(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Coffeebrewery helps you brew better at home')
            ->assertSee('<meta property="og:site_name"', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta name="twitter:card" content="summary">', false)
            ->assertSee('<link rel="canonical" href="http://localhost">', false);
    }

    public function test_every_public_page_has_a_meta_description(): void
    {
        $world = $this->brewWorld();

        $expectations = [
            '/' => 'Coffeebrewery helps you brew better at home',
            '/cheap' => 'Great coffee on a budget',
            '/custom' => 'Answer four quick questions',
            '/recipes' => 'Browse coffee recipes step by step',
            '/recipes/'.$world['recipe']->slug => 'Brew V60 Pour Over with Gayo Highland',
            '/beans/'.$world['bean']->slug => 'flavor notes, acidity and body',
            '/methods/'.$world['method']->slug => 'how it works, the equipment you need',
        ];

        foreach ($expectations as $uri => $description) {
            $response = $this->get($uri)->assertOk();

            // Phrase must sit inside the description tag itself — a plain
            // assertSee would also match stray body text.
            $this->assertMatchesRegularExpression(
                '/<meta name="description" content="[^"]*'.preg_quote($description, '/').'/',
                $response->getContent()
            );
        }
    }

    public function test_recipe_detail_exposes_recipe_json_ld(): void
    {
        $world = $this->brewWorld(withImage: true);

        $response = $this->get('/recipes/'.$world['recipe']->slug)->assertOk();

        $response
            ->assertSee('<link rel="canonical" href="http://localhost/recipes/gayo-v60-morning-brew">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<meta property="og:image"', false);

        $ld = $this->jsonLd($response);

        $this->assertSame('https://schema.org', $ld['@context']);
        $this->assertSame('Recipe', $ld['@type']);
        $this->assertSame('Gayo V60 Morning Brew', $ld['name']);
        $this->assertSame('http://localhost/recipes/gayo-v60-morning-brew', $ld['url']);
        $this->assertSame('https://res.cloudinary.com/demo/image/upload/beans.jpg', $ld['image']);
        $this->assertSame('PT4M', $ld['totalTime']);
        $this->assertSame('1 serving', $ld['recipeYield']);
        $this->assertSame(['15 g coffee beans', '225 ml water'], $ld['recipeIngredient']);
        $this->assertSame('Single Origin', $ld['recipeCategory']);
        $this->assertSame('Gayo Highland, V60 Pour Over', $ld['keywords']);
        $this->assertSame('V60 Pour Over', $ld['tool']['name']);
        $this->assertStringContainsString('Brew V60 Pour Over with Gayo Highland', $ld['description']);

        $this->assertCount(3, $ld['recipeInstructions']);
        $this->assertSame('HowToStep', $ld['recipeInstructions'][0]['@type']);
        $this->assertSame(1, $ld['recipeInstructions'][0]['position']);
        $this->assertSame('Rinse the filter.', $ld['recipeInstructions'][0]['text']);
    }

    public function test_indonesian_locale_translates_description_and_json_ld(): void
    {
        $world = $this->brewWorld();

        $response = $this->withSession(['locale' => 'id'])
            ->get('/recipes/'.$world['recipe']->slug)
            ->assertOk()
            ->assertSee('Seduh V60 Pour Over dengan Gayo Highland')
            ->assertSee('<meta property="og:locale" content="id_ID">', false);

        $ld = $this->jsonLd($response);

        $this->assertStringStartsWith('Seduh V60 Pour Over dengan Gayo Highland', $ld['description']);
        $this->assertSame('PT4M', $ld['totalTime']);   // durations are locale-independent
        $this->assertSame('1 porsi', $ld['recipeYield']);
        $this->assertSame('Rinse the filter.', $ld['recipeInstructions'][0]['text']); // step keys stay English in id.json
    }

    public function test_pages_without_an_image_use_the_summary_card(): void
    {
        $world = $this->brewWorld(withImage: false);

        $this->get('/recipes/'.$world['recipe']->slug)
            ->assertOk()
            ->assertDontSee('<meta property="og:image"', false)
            ->assertDontSee('<meta name="twitter:image"', false)
            ->assertSee('<meta name="twitter:card" content="summary">', false);
    }

    public function test_canonical_strips_query_strings(): void
    {
        $this->get('/recipes?page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="http://localhost/recipes">', false);
    }

    public function test_english_og_locale_is_the_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta property="og:locale" content="en_US">', false);
    }
}
