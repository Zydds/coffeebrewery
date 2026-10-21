<?php

namespace App\Http\Controllers;

use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function welcome()
    {
        $featuredBeans = CoffeeBean::with('category')->take(6)->get();
        $categories = Category::all();
        $methods = BrewMethod::take(4)->get();

        return view('public.home', compact('featuredBeans', 'categories', 'methods'));
    }

    public function beansIndex(Request $request)
    {
        $categories = Category::all();
        $query = CoffeeBean::with('category');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        $beans = $query->get();

        return view('public.beans.index', compact('beans', 'categories'));
    }

    public function beanShow(string $slug)
    {
        $bean = CoffeeBean::with('category')->where('slug', $slug)->firstOrFail();

        return view('public.beans.show', compact('bean'));
    }

    public function methodsIndex()
    {
        $methods = BrewMethod::all();

        return view('public.methods.index', compact('methods'));
    }

    public function methodShow(string $slug)
    {
        $method = BrewMethod::where('slug', $slug)->firstOrFail();

        return view('public.methods.show', compact('method'));
    }
}
