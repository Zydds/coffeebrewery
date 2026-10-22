<?php

namespace Database\Seeders;

use App\Models\BrewMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrewMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'V60 Pour Over',
                'difficulty_level' => 'easy',
                'is_cheap' => false,
                'description' => 'A classic pour-over method using a V60 dripper, producing a clean, bright cup that highlights the coffee\'s origin characteristics.',
                'equipment_needed' => ['V60 Dripper', 'Paper Filter', 'Gooseneck Kettle', 'Scale', 'Timer'],
            ],
            [
                'name' => 'French Press',
                'difficulty_level' => 'easy',
                'is_cheap' => false,
                'description' => 'An immersion brewing method that produces a rich, full-bodied cup with oils and fine particles, using a metal mesh filter.',
                'equipment_needed' => ['French Press', 'Coarse Grinder', 'Kettle', 'Spoon or Paddle'],
            ],
            [
                'name' => 'Aeropress',
                'difficulty_level' => 'medium',
                'is_cheap' => false,
                'description' => 'A versatile brewing method that combines immersion and pressure, producing a clean, smooth cup with low acidity.',
                'equipment_needed' => ['Aeropress', 'Paper Filters', 'Fine Grinder', 'Kettle', 'Scale'],
            ],
            [
                'name' => 'Espresso Machine',
                'difficulty_level' => 'hard',
                'is_cheap' => false,
                'description' => 'A pressure-based brewing method that forces hot water through finely ground coffee, producing a concentrated shot with crema.',
                'equipment_needed' => ['Espresso Machine', 'Burr Grinder', 'Tamper', 'Knock Box', 'Milk Frother'],
            ],
            [
                'name' => 'Sachet Tubruk',
                'difficulty_level' => 'easy',
                'is_cheap' => true,
                'description' => 'Indonesian-style sachet brew: steep the powder straight in hot water like tubruk for a strong, bold cup with a little sediment at the bottom.',
                'equipment_needed' => ['Cup', 'Spoon', 'Kettle or Hot Water Dispenser', 'Coffee Sachet'],
            ],
            [
                'name' => 'Sachet Susu Hangat',
                'difficulty_level' => 'easy',
                'is_cheap' => true,
                'description' => 'Dissolve a milk-coffee sachet into warm milk instead of water for a creamier, café-style hot drink on a budget.',
                'equipment_needed' => ['Cup', 'Spoon', 'Milk', 'Stovetop or Microwave', 'Coffee Sachet'],
            ],
            [
                'name' => 'Es Kopi Sachet',
                'difficulty_level' => 'easy',
                'is_cheap' => true,
                'description' => 'Iced sachet coffee: dissolve the powder in a splash of hot water, then pour over ice with milk for a refreshing cold drink.',
                'equipment_needed' => ['Glass', 'Spoon', 'Ice Cubes', 'Milk', 'Kettle or Hot Water Dispenser', 'Coffee Sachet'],
            ],
        ];

        foreach ($methods as $method) {
            BrewMethod::updateOrCreate(
                ['slug' => Str::slug($method['name'])],
                [
                    'name' => $method['name'],
                    'difficulty_level' => $method['difficulty_level'],
                    'description' => $method['description'],
                    'is_cheap' => $method['is_cheap'],
                    'equipment_needed' => $method['equipment_needed'],
                ]
            );
        }
    }
}
