<?php

namespace App\Http\Controllers;

use App\Models\BrewMethod;
use App\Models\Recipe;
use App\Support\RecipeSeo;
use Illuminate\Http\Request;

class RecipePublicController extends Controller
{
    public function index(Request $request)
    {
        $methods = BrewMethod::orderBy('name')->get();

        $query = Recipe::with(['coffeeBean', 'brewMethod'])
            ->search($request->q)
            ->forMethod($request->method);

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('cheap')) {
            $query->cheap();
        }

        if ($request->boolean('quick')) {
            $query->quick()->orderBy('brew_time_min');
        }

        $recipes = $query->latest()->paginate(9)->withQueryString();

        // A page change gets a slide; a filter/search change gets the card
        // stagger instead. Derived here rather than in the template — Blade's
        // raw-PHP block pass would swallow an inline @php(...) that precedes
        // the ratio-split block.
        $paged = $request->has('page');

        return view('public.recipes.index', compact('recipes', 'methods', 'paged'));
    }

    public function random(Request $request)
    {
        $recipe = Recipe::query()
            ->forPicker(
                cheap: $request->boolean('cheap'),
                quick: $request->boolean('quick'),
                exclude: $request->integer('exclude'),
            )
            ->inRandomOrder()
            ->first();

        if (! $recipe) {
            return redirect()->route('recipes.index');
        }

        return redirect()->route('recipes.show', $recipe->slug);
    }

    public function show(string $slug)
    {
        $recipe = Recipe::with(['coffeeBean.category', 'brewMethod'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.recipes.show', [
            'recipe' => $recipe,
            'seo' => new RecipeSeo($recipe),
        ]);
    }
}
