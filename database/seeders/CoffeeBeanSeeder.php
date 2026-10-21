<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CoffeeBean;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CoffeeBeanSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        $beans = [
            [
                'name' => 'Sidama Guji',
                'origin_country' => 'Ethiopia',
                'category_slug' => 'arabica',
                'acidity_level' => 4,
                'body_level' => 3,
                'flavor_notes' => ['Bright Citrus', 'Stone Fruit', 'Jasmine', 'Bergamot'],
                'recommended_drinks' => ['Pour Over', 'Filter Coffee'],
            ],
            [
                'name' => 'Toraja Kalosi',
                'origin_country' => 'Indonesia',
                'category_slug' => 'robusta',
                'acidity_level' => 2,
                'body_level' => 5,
                'flavor_notes' => ['Earthy', 'Spicy', 'Dark Chocolate', 'Herbal'],
                'recommended_drinks' => ['Espresso Blend', 'French Press'],
            ],
            [
                'name' => 'Gayo Aceh',
                'origin_country' => 'Indonesia',
                'category_slug' => 'arabica',
                'acidity_level' => 3,
                'body_level' => 4,
                'flavor_notes' => ['Full Body', 'Tobacco', 'Dark Fruit', 'Caramel'],
                'recommended_drinks' => ['French Press', 'Espresso'],
            ],
            [
                'name' => 'Kintamani Bali',
                'origin_country' => 'Indonesia',
                'category_slug' => 'arabica',
                'acidity_level' => 3,
                'body_level' => 4,
                'flavor_notes' => ['Tropical Fruit', 'Chocolate', 'Floral', 'Spice'],
                'recommended_drinks' => ['Pour Over', 'Aeropress'],
            ],
            [
                'name' => 'Colombian Supremo',
                'origin_country' => 'Colombia',
                'category_slug' => 'arabica',
                'acidity_level' => 4,
                'body_level' => 3,
                'flavor_notes' => ['Sweet', 'Nutty', 'Red Apple', 'Caramel'],
                'recommended_drinks' => ['Espresso', 'Pour Over'],
            ],
            [
                'name' => 'Java Preanger',
                'origin_country' => 'Indonesia',
                'category_slug' => 'robusta',
                'acidity_level' => 1,
                'body_level' => 5,
                'flavor_notes' => ['Strong', 'Earthy', 'Peppery', 'Dark Chocolate'],
                'recommended_drinks' => ['Espresso Blend', 'French Press'],
            ],
        ];

        foreach ($beans as $bean) {
            CoffeeBean::create([
                'category_id' => $categories[$bean['category_slug']],
                'name' => $bean['name'],
                'slug' => Str::slug($bean['name']),
                'origin_country' => $bean['origin_country'],
                'acidity_level' => $bean['acidity_level'],
                'body_level' => $bean['body_level'],
                'flavor_notes' => $bean['flavor_notes'],
                'recommended_drinks' => $bean['recommended_drinks'],
            ]);
        }
    }
}
