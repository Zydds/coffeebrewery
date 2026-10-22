<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'beans' => CoffeeBean::count(),
            'beans_regular' => CoffeeBean::where('is_cheap', false)->count(),
            'beans_cheap' => CoffeeBean::where('is_cheap', true)->count(),
            'recipes' => Recipe::count(),
            'recipes_featured' => Recipe::where('is_featured', true)->count(),
            'recipes_cheap' => Recipe::where('cheap', true)->count(),
            'methods' => BrewMethod::count(),
            'methods_regular' => BrewMethod::where('is_cheap', false)->count(),
            'methods_cheap' => BrewMethod::where('is_cheap', true)->count(),
            'categories' => Category::count(),
        ];

        $recentBeans = CoffeeBean::with('category')->latest()->take(5)->get();
        $recentRecipes = Recipe::with(['coffeeBean', 'brewMethod'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('counts', 'recentBeans', 'recentRecipes'));
    }
}
