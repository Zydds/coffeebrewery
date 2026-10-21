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
        $exclude = $request->integer('exclude');

        $recipe = Recipe::with(['coffeeBean.category', 'brewMethod'])
            ->when($exclude, fn ($q) => $q->whereKeyNot($exclude))
            ->inRandomOrder()
            ->first();

        if (! $recipe) {
            return response()->json(['message' => 'No recipes found.'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'ratio' => $recipe->ratio,
                'water_temp' => $recipe->water_temp_c.'°C',
                'grind_size' => $recipe->grind_size,
                'step_by_step' => $recipe->step_by_step,
                'bean' => [
                    'name' => $recipe->coffeeBean->name,
                    'category' => $recipe->coffeeBean->category->name,
                ],
                'method' => [
                    'name' => $recipe->brewMethod->name,
                ],
            ],
        ]);
    }
}
