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
                'description' => 'A classic pour-over method using a V60 dripper, producing a clean, bright cup that highlights the coffee\'s origin characteristics.',
                'equipment_needed' => ['V60 Dripper', 'Paper Filter', 'Gooseneck Kettle', 'Scale', 'Timer'],
            ],
            [
                'name' => 'French Press',
                'difficulty_level' => 'easy',
                'description' => 'An immersion brewing method that produces a rich, full-bodied cup with oils and fine particles, using a metal mesh filter.',
                'equipment_needed' => ['French Press', 'Coarse Grinder', 'Kettle', 'Spoon or Paddle'],
            ],
            [
                'name' => 'Aeropress',
                'difficulty_level' => 'medium',
                'description' => 'A versatile brewing method that combines immersion and pressure, producing a clean, smooth cup with low acidity.',
                'equipment_needed' => ['Aeropress', 'Paper Filters', 'Fine Grinder', 'Kettle', 'Scale'],
            ],
            [
                'name' => 'Espresso Machine',
                'difficulty_level' => 'hard',
                'description' => 'A pressure-based brewing method that forces hot water through finely ground coffee, producing a concentrated shot with crema.',
                'equipment_needed' => ['Espresso Machine', 'Burr Grinder', 'Tamper', 'Knock Box', 'Milk Frother'],
            ],
        ];

        foreach ($methods as $method) {
            BrewMethod::create([
                'name' => $method['name'],
                'slug' => Str::slug($method['name']),
                'difficulty_level' => $method['difficulty_level'],
                'description' => $method['description'],
                'equipment_needed' => $method['equipment_needed'],
            ]);
        }
    }
}
