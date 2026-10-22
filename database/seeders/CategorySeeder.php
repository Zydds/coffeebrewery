<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Arabica',
                'slug' => 'arabica',
                'description' => 'Arabica beans are known for their smooth, sweet flavor with notes of sugar and fruit. They grow on bushes at high altitudes and account for about 60-70% of the world\'s coffee production.',
            ],
            [
                'name' => 'Robusta',
                'slug' => 'robusta',
                'description' => 'Robusta beans are stronger and more bitter than Arabica, with a higher caffeine content. They grow at lower altitudes and are known for their strong, earthy flavor and good crema in espresso blends.',
            ],
            [
                'name' => 'Liberica',
                'slug' => 'liberica',
                'description' => 'Liberica beans are rare and distinctive, offering a unique taste with hints of floral and woody notes. They are less acidic than Arabica and have a fuller body, grown primarily in the Philippines and West Africa.',
            ],
            [
                'name' => 'Sachet',
                'slug' => 'sachet',
                'description' => 'Powdered coffee sachets brewed like tubruk — full flavour, with a little sediment left in the cup.',
            ],
            [
                'name' => 'Instant',
                'slug' => 'instant',
                'description' => 'Freeze-dried soluble coffees that dissolve completely — no residue in the cup.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
