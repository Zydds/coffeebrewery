<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CoffeeBean;
use Illuminate\Database\Seeder;

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
                'price' => 95000,
                'price_unit' => '100 gr',
                'is_cheap' => false,
                'flavor_notes' => ['Bright Citrus', 'Stone Fruit', 'Jasmine', 'Bergamot'],
                'recommended_drinks' => ['Pour Over', 'Filter Coffee'],
            ],
            [
                'name' => 'Toraja Kalosi',
                'origin_country' => 'Indonesia',
                'category_slug' => 'robusta',
                'acidity_level' => 2,
                'body_level' => 5,
                'price' => 75000,
                'price_unit' => '100 gr',
                'is_cheap' => false,
                'flavor_notes' => ['Earthy', 'Spicy', 'Dark Chocolate', 'Herbal'],
                'recommended_drinks' => ['Espresso Blend', 'French Press'],
            ],
            [
                'name' => 'Gayo Aceh',
                'origin_country' => 'Indonesia',
                'category_slug' => 'arabica',
                'acidity_level' => 3,
                'body_level' => 4,
                'price' => 85000,
                'price_unit' => '100 gr',
                'is_cheap' => false,
                'flavor_notes' => ['Full Body', 'Tobacco', 'Dark Fruit', 'Caramel'],
                'recommended_drinks' => ['French Press', 'Espresso'],
            ],
            [
                'name' => 'Kintamani Bali',
                'origin_country' => 'Indonesia',
                'category_slug' => 'arabica',
                'acidity_level' => 3,
                'body_level' => 4,
                'price' => 90000,
                'price_unit' => '100 gr',
                'is_cheap' => false,
                'flavor_notes' => ['Tropical Fruit', 'Chocolate', 'Floral', 'Spice'],
                'recommended_drinks' => ['Pour Over', 'Aeropress'],
            ],
            [
                'name' => 'Colombian Supremo',
                'origin_country' => 'Colombia',
                'category_slug' => 'arabica',
                'acidity_level' => 4,
                'body_level' => 3,
                'price' => 110000,
                'price_unit' => '100 gr',
                'is_cheap' => false,
                'flavor_notes' => ['Sweet', 'Nutty', 'Red Apple', 'Caramel'],
                'recommended_drinks' => ['Espresso', 'Pour Over'],
            ],
            [
                'name' => 'Java Preanger',
                'origin_country' => 'Indonesia',
                'category_slug' => 'robusta',
                'acidity_level' => 1,
                'body_level' => 5,
                'price' => 70000,
                'price_unit' => '100 gr',
                'is_cheap' => false,
                'flavor_notes' => ['Strong', 'Earthy', 'Peppery', 'Dark Chocolate'],
                'recommended_drinks' => ['Espresso Blend', 'French Press'],
            ],
            [
                'name' => 'Kapal Api Special Hitam',
                'origin_country' => 'Indonesia',
                'category_slug' => 'sachet',
                'acidity_level' => 2,
                'body_level' => 4,
                'price' => 1800,
                'price_unit' => 'sachet',
                'is_cheap' => true,
                'flavor_notes' => ['Bold', 'Bitter Chocolate', 'Robust'],
                'recommended_drinks' => ['Hot Sachet Brew', 'Tubruk'],
            ],
            [
                'name' => 'Kapal Api Special Putih',
                'origin_country' => 'Indonesia',
                'category_slug' => 'sachet',
                'acidity_level' => 2,
                'body_level' => 3,
                'price' => 2000,
                'price_unit' => 'sachet',
                'is_cheap' => true,
                'flavor_notes' => ['Sweet', 'Creamy', 'Mild'],
                'recommended_drinks' => ['Hot Sachet Brew'],
            ],
            [
                'name' => 'ABC Susu',
                'origin_country' => 'Indonesia',
                'category_slug' => 'sachet',
                'acidity_level' => 1,
                'body_level' => 3,
                'price' => 2500,
                'price_unit' => 'sachet',
                'is_cheap' => true,
                'flavor_notes' => ['Milky', 'Sweet', 'Caramel'],
                'recommended_drinks' => ['Sachet Susu Hangat', 'Iced Sachet Latte'],
            ],
            [
                'name' => 'Nescafe Classic',
                'origin_country' => 'Indonesia',
                'category_slug' => 'instant',
                'acidity_level' => 3,
                'body_level' => 3,
                'price' => 2700,
                'price_unit' => 'sachet',
                'is_cheap' => true,
                'flavor_notes' => ['Roasty', 'Smooth', 'Classic'],
                'recommended_drinks' => ['Hot Sachet Brew', 'Iced Sachet Latte'],
            ],
            [
                'name' => 'Nescafe Ice Roast',
                'origin_country' => 'Indonesia',
                'category_slug' => 'instant',
                'acidity_level' => 4,
                'body_level' => 2,
                'price' => 3500,
                'price_unit' => 'sachet',
                'is_cheap' => true,
                'flavor_notes' => ['Citrus', 'Refreshing', 'Light'],
                'recommended_drinks' => ['Es Kopi Sachet'],
            ],
            [
                'name' => 'Good Day Cappuccino',
                'origin_country' => 'Indonesia',
                'category_slug' => 'sachet',
                'acidity_level' => 1,
                'body_level' => 3,
                'price' => 2200,
                'price_unit' => 'sachet',
                'is_cheap' => true,
                'flavor_notes' => ['Creamy', 'Chocolatey', 'Sweet'],
                'recommended_drinks' => ['Sachet Susu Hangat'],
            ],
            [
                'name' => 'Torabika Tjap Nyonya',
                'origin_country' => 'Indonesia',
                'category_slug' => 'sachet',
                'acidity_level' => 2,
                'body_level' => 4,
                'price' => 1700,
                'price_unit' => 'sachet',
                'is_cheap' => true,
                'flavor_notes' => ['Strong', 'Cocoa', 'Traditional'],
                'recommended_drinks' => ['Hot Sachet Brew', 'Tubruk'],
            ],
        ];

        foreach ($beans as $bean) {
            CoffeeBean::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($bean['name'])],
                [
                    'category_id' => $categories[$bean['category_slug']],
                    'name' => $bean['name'],
                    'origin_country' => $bean['origin_country'],
                    'acidity_level' => $bean['acidity_level'],
                    'body_level' => $bean['body_level'],
                    'price' => $bean['price'],
                    'price_unit' => $bean['price_unit'],
                    'is_cheap' => $bean['is_cheap'],
                    'flavor_notes' => $bean['flavor_notes'],
                    'recommended_drinks' => $bean['recommended_drinks'],
                ]
            );
        }
    }
}
