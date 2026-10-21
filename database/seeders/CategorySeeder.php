<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
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
        ]);
    }
}
