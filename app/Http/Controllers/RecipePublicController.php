<?php

namespace App\Http\Controllers;

use App\Models\BrewMethod;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipePublicController extends Controller
{
    public function index(Request $request)
    {
        $methods = BrewMethod::orderBy('name')->get();

        $query = Recipe::with(['coffeeBean', 'brewMethod']);

        if ($q = trim((string) $request->q)) {
            $needle = '%'.mb_strtolower($q).'%';
            $query->where(fn ($w) => $w
                ->whereRaw('LOWER(title) LIKE ?', [$needle])
                ->orWhereHas('coffeeBean', fn ($b) => $b->whereRaw('LOWER(name) LIKE ?', [$needle]))
                ->orWhereHas('brewMethod', fn ($m) => $m->whereRaw('LOWER(name) LIKE ?', [$needle])));
        }

        if ($request->filled('method')) {
            $query->whereHas('brewMethod', fn ($q) => $q->where('slug', $request->method));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->boolean('cheap')) {
            $query->where('cheap', true);
        }

        if ($request->boolean('quick')) {
            $query->whereNotNull('brew_time_min')
                ->where('brew_time_min', '<=', 5)
                ->orderBy('brew_time_min');
        }

        $recipes = $query->latest()->paginate(9)->withQueryString();

        return view('public.recipes.index', compact('recipes', 'methods'));
    }

    public function random(Request $request)
    {
        $recipe = Recipe::query()
            ->when($request->boolean('quick'), fn ($q) => $q
                ->whereNotNull('brew_time_min')
                ->where('brew_time_min', '<=', 5))
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

        return view('public.recipes.show', compact('recipe'));
    }
}
