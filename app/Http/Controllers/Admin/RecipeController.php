<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRecipeRequest;
use App\Models\BrewMethod;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with(['coffeeBean', 'brewMethod'])->latest()->get();

        return view('admin.recipes.index', compact('recipes'));
    }

    public function create()
    {
        $beans = CoffeeBean::all();
        $methods = BrewMethod::all();

        return view('admin.recipes.create', compact('beans', 'methods'));
    }

    public function store(StoreRecipeRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        Recipe::create($data);

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Recipe created.');
    }

    public function edit(Recipe $recipe)
    {
        $beans = CoffeeBean::all();
        $methods = BrewMethod::all();

        return view('admin.recipes.edit', compact('recipe', 'beans', 'methods'));
    }

    public function update(StoreRecipeRequest $request, Recipe $recipe)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        $recipe->update($data);

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Recipe updated.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Recipe deleted.');
    }
}
