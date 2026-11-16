<?php

namespace App\Http\Controllers;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function welcome(Request $request)
    {
        $categories = Category::whereHas('coffeeBeans', fn ($q) => $q->where('is_cheap', false))->get();
        $featuredBeans = $this->beanQuery($request)->latest()->paginate(8, ['*'], 'bean_page')->withQueryString();
        $methods = $this->methodQuery($request)->paginate(8, ['*'], 'method_page')->withQueryString();

        // Site-wide totals for the home proof strip — deliberately NOT the
        // paginator totals, which shrink as filters are applied.
        $proof = [
            'beans' => CoffeeBean::count(),
            'recipes' => Recipe::count(),
            'methods' => BrewMethod::count(),
        ];

        // The catalog below lists regular beans only, so the "Showing" line
        // names that scope and links the rest — otherwise 6 listed vs 14 in
        // the proof strip reads as a contradiction.
        $cheapBeans = CoffeeBean::where('is_cheap', true)->count();

        return view('public.home', compact('featuredBeans', 'categories', 'methods', 'proof', 'cheapBeans'));
    }

    public function cheapIndex(Request $request)
    {
        $categories = Category::whereHas('coffeeBeans', fn ($q) => $q->where('is_cheap', true))->get();
        $beans = $this->beanQuery($request, cheap: true)->latest()->paginate(8, ['*'], 'bean_page')->withQueryString();
        $methods = $this->methodQuery($request, cheap: true)->paginate(8, ['*'], 'method_page')->withQueryString();

        return view('public.cheap.index', compact('categories', 'beans', 'methods'));
    }

    private function beanQuery(Request $request, bool $cheap = false)
    {
        $query = CoffeeBean::with('category')->where('is_cheap', $cheap);

        if ($q = trim((string) $request->q)) {
            $needle = '%'.mb_strtolower($q).'%';
            $query->where(fn ($w) => $w
                ->whereRaw('LOWER(name) LIKE ?', [$needle])
                ->orWhereRaw('LOWER(origin_country) LIKE ?', [$needle]));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        return $query;
    }

    private function methodQuery(Request $request, bool $cheap = false)
    {
        $query = BrewMethod::withCount('recipes')->where('is_cheap', $cheap);

        if ($q = trim((string) $request->method_q)) {
            $needle = '%'.mb_strtolower($q).'%';
            $query->where(fn ($w) => $w
                ->whereRaw('LOWER(name) LIKE ?', [$needle])
                ->orWhereRaw('LOWER(description) LIKE ?', [$needle]));
        }

        return $query;
    }

    public function beanShow(string $slug)
    {
        // recipes.brewMethod eager-loaded for the "Recipes with this
        // bean" section (avoids N+1 on the recipe method chips).
        $bean = CoffeeBean::with(['category', 'recipes.brewMethod'])->where('slug', $slug)->firstOrFail();

        return view('public.beans.show', compact('bean'));
    }

    public function methodShow(string $slug)
    {
        $method = BrewMethod::where('slug', $slug)->firstOrFail();

        return view('public.methods.show', compact('method'));
    }
}
