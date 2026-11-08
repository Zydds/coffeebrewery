<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RandomRecipeController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $recipe = Recipe::with(['coffeeBean.category', 'brewMethod'])
            ->forPicker(
                cheap: $request->boolean('cheap'),
                quick: $request->boolean('quick'),
                exclude: $request->integer('exclude'),
            )
            ->inRandomOrder()
            ->first();

        if (! $recipe) {
            return response()->json(['message' => 'No recipes found.'], 404);
        }

        return response()->json([
            'data' => [
                ...$recipe->summary(),
                'step_by_step' => $recipe->step_by_step
                    ? array_map(fn ($s) => (string) __($s), $recipe->step_by_step)
                    : $recipe->step_by_step,
            ],
        ]);
    }
}
