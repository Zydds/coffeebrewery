<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeSuggestController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ingredients' => 'required|in:sachet,beans,ground',
            'time' => 'nullable|in:quick,ten,rush',
            'gear' => 'nullable|in:mug,french-press,v60,aeropress,espresso',
            'taste' => 'nullable|in:strong,mild,fruity',
            'drink' => 'nullable|in:black,milk',
        ]);

        $matches = Recipe::with(['coffeeBean.category', 'brewMethod'])
            ->get()
            ->map(fn (Recipe $recipe) => [
                'recipe' => $recipe,
                'score' => $this->score($recipe, $data),
            ])
            ->sortByDesc('score')
            ->take(3)
            ->map(fn (array $item) => $this->present($item['recipe'], $item['score']))
            ->values();

        return response()->json(['data' => $matches]);
    }

    private function score(Recipe $recipe, array $answers): int
    {
        $score = 0;
        $method = (string) $recipe->brewMethod?->slug;
        $isSachetMethod = str_starts_with($method, 'sachet-') || $method === 'es-kopi-sachet';
        $bean = $recipe->coffeeBean;
        $notes = strtolower(implode(' ', (array) ($bean?->flavor_notes ?? [])));
        $body = (int) ($bean?->body_level ?? 0);
        $acidity = (int) ($bean?->acidity_level ?? 0);
        $category = strtolower((string) $bean?->category?->name);
        $time = (int) $recipe->brew_time_min;

        // 1. What coffee do you have?
        if ($answers['ingredients'] === 'sachet') {
            $score += $isSachetMethod ? 40 : -60;
        } elseif ($answers['ingredients'] === 'beans') {
            $score += $isSachetMethod ? -60 : 25;
        } else { // ground
            $score += $isSachetMethod ? 0 : 10;
            if (in_array($method, ['french-press', 'espresso-machine'], true)) {
                $score += 10;
            }
        }

        // 2. How much time?
        if ($answers['time'] ?? null) {
            $score += match ($answers['time']) {
                'quick' => $time <= 5 ? 30 : -40,
                'ten' => $time <= 10 ? 20 : -30,
                'rush' => 10,
            };
        }

        // 3. What gear?
        if (($answers['gear'] ?? null) === 'mug') {
            $score += $isSachetMethod ? 25 : -5;
        } elseif (isset($answers['gear'])) {
            $gearMethod = [
                'french-press' => 'french-press',
                'v60' => 'v60-pour-over',
                'aeropress' => 'aeropress',
                'espresso' => 'espresso-machine',
            ][$answers['gear']] ?? null;

            if ($gearMethod) {
                $score += $method === $gearMethod ? 40 : -25;
            }
        }

        // 4. Taste preference?
        if (($answers['taste'] ?? null) === 'strong') {
            $score += $body >= 4 ? 15 : 0;
            $score += $category === 'robusta' ? 10 : 0;
            $score += str_contains($notes, 'strong') || str_contains($notes, 'bold')
                || str_contains($notes, 'earthy') || str_contains($notes, 'robust') ? 10 : 0;
        } elseif (($answers['taste'] ?? null) === 'mild') {
            $score += $body <= 3 ? 10 : 0;
            $score += str_contains($notes, 'smooth') || str_contains($notes, 'creamy')
                || str_contains($notes, 'mild') || str_contains($notes, 'sweet')
                || str_contains($notes, 'milky') ? 12 : 0;
        } elseif (($answers['taste'] ?? null) === 'fruity') {
            $score += str_contains($notes, 'citrus') || str_contains($notes, 'fruit')
                || str_contains($notes, 'jasmine') || str_contains($notes, 'bright')
                || str_contains($notes, 'floral') || str_contains($notes, 'bergamot') ? 18 : 0;
            $score += $acidity >= 4 ? 8 : 0;
        }

        // 5. Milk or black?
        $isMilky = str_contains(strtolower((string) $recipe->title), 'susu') || str_contains($notes, 'milky');
        if (($answers['drink'] ?? null) === 'milk') {
            $score += $isMilky ? 25 : -5;
        } elseif (($answers['drink'] ?? null) === 'black') {
            $score += $isMilky ? -15 : 5;
        }

        return $score;
    }

    private function present(Recipe $recipe, int $score): array
    {
        return [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'slug' => $recipe->slug,
            'brew_time_min' => $recipe->brew_time_min,
            'ratio' => $recipe->ratio,
            'water_temp' => $recipe->water_temp_c.'°C',
            'grind_size' => $recipe->grind_size,
            'cheap' => (bool) $recipe->cheap,
            'score' => $score,
            'bean' => [
                'name' => $recipe->coffeeBean?->name,
                'category' => $recipe->coffeeBean?->category?->name,
            ],
            'method' => [
                'name' => $recipe->brewMethod?->name,
            ],
        ];
    }
}
