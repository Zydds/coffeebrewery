<?php

namespace App\Http\Controllers;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function welcome(Request $request)
    {
        $categories = Category::whereHas('coffeeBeans', fn ($q) => $q->where('is_cheap', false))->get();
        $featuredBeans = $this->beanQuery($request)->latest()->paginate(8)->withQueryString();
        $methods = $this->methodQuery($request)->paginate(8)->withQueryString();

        return view('public.home', compact('featuredBeans', 'categories', 'methods'));
    }

    public function cheapIndex(Request $request)
    {
        $categories = Category::whereHas('coffeeBeans', fn ($q) => $q->where('is_cheap', true))->get();
        $beans = $this->beanQuery($request, cheap: true)->latest()->paginate(8)->withQueryString();
        $methods = $this->methodQuery($request, cheap: true)->paginate(8)->withQueryString();

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
        $bean = CoffeeBean::with('category')->where('slug', $slug)->firstOrFail();

        return view('public.beans.show', compact('bean'));
    }

    public function methodShow(string $slug)
    {
        $method = BrewMethod::where('slug', $slug)->firstOrFail();

        return view('public.methods.show', compact('method'));
    }
}
