<?php

namespace Database\Seeders;

use App\Models\BrewMethod;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $beans = CoffeeBean::all()->keyBy('name');
        $methods = BrewMethod::all()->keyBy('name');

        $recipes = [
            [
                'bean' => 'Sidama Guji',
                'method' => 'V60 Pour Over',
                'title' => 'Ethiopian Bright Citrus Pour Over',
                'water_temp_c' => 93,
                'grind_size' => 'Medium-Fine',
                'ratio' => '1:15',
                'brew_time_min' => 8,
                'is_featured' => true,
                'cheap' => false,
                'steps' => [
                    'Place V60 on scale and position paper filter inside. Rinse filter with hot water.',
                    'Add medium-fine ground coffee (20g) to the filter.',
                    'Start timer and pour 40g of water (93°C) in slow circular motions to saturate grounds.',
                    'Wait 30 seconds for bloom to complete.',
                    'Continue pouring water in steady spiral until you reach 300g total. Total brew time: 2:30-3:00.',
                    'Remove dripper and serve immediately. Enjoy the bright, citrusy notes.',
                ],
            ],
            [
                'bean' => 'Toraja Kalosi',
                'method' => 'French Press',
                'title' => 'Indonesian Earthy French Press',
                'water_temp_c' => 90,
                'grind_size' => 'Coarse',
                'ratio' => '1:13',
                'brew_time_min' => 10,
                'is_featured' => false,
                'cheap' => true,
                'steps' => [
                    'Grind beans to coarse setting. Measure 30g of coffee.',
                    'Preheat French press with hot water, then discard.',
                    'Add coffee grounds and pour 400g of water at 90°C.',
                    'Stir gently with a spoon to ensure all grounds are saturated.',
                    'Place lid on with plunger pulled up. Steep for 4 minutes.',
                    'Press plunger down slowly and steadily. Pour and serve.',
                ],
            ],
            [
                'bean' => 'Gayo Aceh',
                'method' => 'Espresso Machine',
                'title' => 'Aceh Full-Bodied Espresso',
                'water_temp_c' => 92,
                'grind_size' => 'Fine',
                'ratio' => '1:2',
                'brew_time_min' => 4,
                'is_featured' => true,
                'cheap' => false,
                'steps' => [
                    'Grind 20g of coffee to fine consistency, dosing into portafilter.',
                    'Distribute grounds evenly with a distribution tool.',
                    'Tamper with 30lbs of pressure for even tamp.',
                    'Extract for 25-30 seconds, aiming for 40g output.',
                    'Check for thick crema and rich body. Knock puck into knock box.',
                    'Serve immediately as espresso or use for milk drinks.',
                ],
            ],
            [
                'bean' => 'Kintamani Bali',
                'method' => 'Aeropress',
                'title' => 'Bali Tropical Aeropress Recipe',
                'water_temp_c' => 88,
                'grind_size' => 'Fine',
                'ratio' => '1:15',
                'brew_time_min' => 7,
                'is_featured' => false,
                'cheap' => false,
                'steps' => [
                    'Assemble Aeropress with paper filter. Rinse filter with hot water.',
                    'Add 15g of fine-ground coffee to chamber using inverted method.',
                    'Pour 150ml of water at 88°C over grounds. Stir for 10 seconds.',
                    'Attach cap and steep for 1 minute.',
                    'Flip onto cup and press gently for 20-30 seconds.',
                    'Dilute with 50ml hot water if desired. Serve and enjoy.',
                ],
            ],
            [
                'bean' => 'Colombian Supremo',
                'method' => 'V60 Pour Over',
                'title' => 'Colombian Sweet Nutty Pour Over',
                'water_temp_c' => 93,
                'grind_size' => 'Medium',
                'ratio' => '1:16',
                'brew_time_min' => 8,
                'is_featured' => false,
                'cheap' => false,
                'steps' => [
                    'Set up V60 with paper filter and rinse with hot water.',
                    'Add 20g of medium-ground coffee and level the bed.',
                    'Start timer and pour 40g water for bloom. Wait 45 seconds.',
                    'Pour remaining water in concentric circles until 320g is reached.',
                    'Allow total drawdown of 2:30-3:00 minutes.',
                    'Discard filter and serve the sweet, nutty cup.',
                ],
            ],
            [
                'bean' => 'Java Preanger',
                'method' => 'French Press',
                'title' => 'Java Strong Earthy French Press',
                'water_temp_c' => 92,
                'grind_size' => 'Coarse',
                'ratio' => '1:12',
                'brew_time_min' => 10,
                'is_featured' => false,
                'cheap' => true,
                'steps' => [
                    'Use coarse grind. Add 32g coffee to preheated French press.',
                    'Pour 400g water at 92°C, saturating all grounds.',
                    'Stir vigorously to break crust and ensure full saturation.',
                    'Let steep for 4-5 minutes for maximum body and strength.',
                    'Press plunger down slowly. Pour carefully to avoid sediment.',
                    'Serve immediately for the strongest, earthiest flavor.',
                ],
            ],
            [
                'bean' => 'Sidama Guji',
                'method' => 'Aeropress',
                'title' => 'Sidama Jasmine Aeropress',
                'water_temp_c' => 85,
                'grind_size' => 'Medium-Fine',
                'ratio' => '1:15',
                'brew_time_min' => 7,
                'is_featured' => false,
                'cheap' => false,
                'steps' => [
                    'Use medium-fine grind. Place 15g coffee in inverted Aeropress.',
                    'Pour 200ml water at 85°C slowly over grounds.',
                    'Stir gently for 10 seconds to ensure even extraction.',
                    'Steep for 2 minutes, then attach cap and flip.',
                    'Press gently for 30 seconds into serving cup.',
                    'The lower temperature preserves delicate jasmine floral notes.',
                ],
            ],
            [
                'bean' => 'Kintamani Bali',
                'method' => 'Espresso Machine',
                'title' => 'Kintamani Tropical Espresso',
                'water_temp_c' => 93,
                'grind_size' => 'Fine',
                'ratio' => '1:2.2',
                'brew_time_min' => 4,
                'is_featured' => false,
                'cheap' => false,
                'steps' => [
                    'Grind 19g to fine consistency for double shot.',
                    'Distribute and tamp evenly with moderate pressure.',
                    'Extract for 28-30 seconds, targeting 42g yield.',
                    'Observe caramel-colored crema and check for balance.',
                    'Serve as straight espresso to appreciate the tropical fruit notes.',
                    'Clean portafilter promptly for hygiene.',
                ],
            ],
            [
                'bean' => 'Kapal Api Special Hitam',
                'method' => 'Sachet Tubruk',
                'title' => 'Kapal Api Tubruk Sachet',
                'water_temp_c' => 95,
                'grind_size' => 'Pre-ground sachet',
                'ratio' => '1 sachet : 150 ml',
                'brew_time_min' => 2,
                'is_featured' => false,
                'cheap' => true,
                'steps' => [
                    'Empty one Kapal Api sachet into your cup.',
                    'Pour 150 ml of water at 95°C straight over the powder.',
                    'Stir once, then let it sit for 2 minutes so the grounds settle like tubruk.',
                    'Drink from the top and leave the sediment behind.',
                ],
            ],
            [
                'bean' => 'ABC Susu',
                'method' => 'Sachet Susu Hangat',
                'title' => 'ABC Susu Hangat',
                'water_temp_c' => 90,
                'grind_size' => 'Pre-ground sachet',
                'ratio' => '1 sachet : 150 ml milk',
                'brew_time_min' => 4,
                'is_featured' => false,
                'cheap' => true,
                'steps' => [
                    'Warm 150 ml of milk until steaming but not boiling.',
                    'Pour the warm milk over one ABC Susu sachet in a cup.',
                    'Stir well until the powder is fully dissolved.',
                    'Enjoy a creamy, sweet café-style drink for a fraction of the price.',
                ],
            ],
            [
                'bean' => 'Nescafe Ice Roast',
                'method' => 'Es Kopi Sachet',
                'title' => 'Nescafe Ice Roast Citrus Iced',
                'water_temp_c' => 90,
                'grind_size' => 'Pre-ground sachet',
                'ratio' => '1 sachet : 50 ml hot water + 150 ml cold water',
                'brew_time_min' => 3,
                'is_featured' => false,
                'cheap' => true,
                'steps' => [
                    'Dissolve one Nescafe Ice Roast sachet in 50 ml of hot water and stir.',
                    'Fill a glass with ice cubes.',
                    'Pour the concentrated coffee over the ice, then top with 150 ml cold water.',
                    'Stir once and serve immediately — bright, citrusy and refreshing.',
                ],
            ],
            [
                'bean' => 'Colombian Supremo',
                'method' => 'Aeropress',
                'title' => 'Simple Aeropress Weekday Cup',
                'water_temp_c' => 90,
                'grind_size' => 'Medium-Fine',
                'ratio' => '1:14',
                'brew_time_min' => 6,
                'is_featured' => false,
                'cheap' => false,
                'steps' => [
                    'Add 16g medium-fine coffee to the Aeropress chamber.',
                    'Pour 220ml of water at 90°C and stir gently.',
                    'Steep for 1 minute, then press slowly for 30 seconds.',
                    'Serve as-is or dilute with a splash of hot water.',
                ],
            ],
            [
                'bean' => 'Java Preanger',
                'method' => 'Espresso Machine',
                'title' => 'Quick Double Espresso',
                'water_temp_c' => 93,
                'grind_size' => 'Fine',
                'ratio' => '1:2',
                'brew_time_min' => 3,
                'is_featured' => false,
                'cheap' => false,
                'steps' => [
                    'Grind 18g coffee fine and lock the portafilter in.',
                    'Tamp level with light, even pressure.',
                    'Pull for 25-30 seconds until you get roughly 36g of shot.',
                    'Serve right away.',
                ],
            ],
            [
                'bean' => 'Gayo Aceh',
                'method' => 'French Press',
                'title' => 'Easy Morning French Press',
                'water_temp_c' => 94,
                'grind_size' => 'Coarse',
                'ratio' => '1:15',
                'brew_time_min' => 10,
                'is_featured' => false,
                'cheap' => false,
                'steps' => [
                    'Add 30g coarse coffee to the press.',
                    'Pour 450ml of water at 94°C and give one stir.',
                    'Wait 4 minutes, skim the floating grounds off the top.',
                    'Press down slowly and pour.',
                ],
            ],
        ];

        foreach ($recipes as $recipe) {
            $bean = $beans[$recipe['bean']] ?? null;
            $method = $methods[$recipe['method']] ?? null;

            if ($bean && $method) {
                Recipe::updateOrCreate(
                    ['slug' => Str::slug($recipe['title'])],
                    [
                        'coffee_bean_id' => $bean->id,
                        'brew_method_id' => $method->id,
                        'title' => $recipe['title'],
                        'water_temp_c' => $recipe['water_temp_c'],
                        'grind_size' => $recipe['grind_size'],
                        'ratio' => $recipe['ratio'],
                        'brew_time_min' => $recipe['brew_time_min'],
                        'step_by_step' => $recipe['steps'],
                        'is_featured' => $recipe['is_featured'],
                        'cheap' => $recipe['cheap'],
                    ]
                );
            }
        }
    }
}
