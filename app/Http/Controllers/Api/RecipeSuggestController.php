<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Services\RecipeMatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeSuggestController extends Controller
{
    public function __construct(
        private RecipeMatcher $matcher
    ) {}

    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ingredients' => 'required|in:sachet,beans,ground',
            'time' => 'nullable|in:quick,ten,rush',
            'gear' => 'nullable|in:mug,french-press,v60,aeropress,espresso',
            'taste' => 'nullable|in:strong,mild,fruity',
            'drink' => 'nullable|in:black,milk',
        ]);

        $matches = $this->matcher
            ->rank(Recipe::with(['coffeeBean.category', 'brewMethod'])->get(), $data)
            ->map(fn (array $match) => [
                ...$match['recipe']->summary(),
                'cheap' => (bool) $match['recipe']->cheap,
                'score' => $match['score'],
            ]);

        return response()->json(['data' => $matches]);
    }
}
