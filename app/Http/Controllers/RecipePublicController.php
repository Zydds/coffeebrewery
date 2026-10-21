<?php

namespace App\Http\Controllers;

use App\Models\Recipe;

class RecipePublicController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with(['coffeeBean', 'brewMethod'])->latest()->get();

        return view('public.recipes.index', compact('recipes'));
    }

    public function show(string $slug)
    {
        $recipe = Recipe::with(['coffeeBean.category', 'brewMethod'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.recipes.show', compact('recipe'));
    }
}
