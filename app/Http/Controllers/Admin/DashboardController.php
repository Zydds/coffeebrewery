<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrewMethod;
use App\Models\CoffeeBean;
use App\Models\Recipe;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'beans' => CoffeeBean::count(),
            'recipes' => Recipe::count(),
            'methods' => BrewMethod::count(),
        ];

        $recentBeans = CoffeeBean::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact('counts', 'recentBeans'));
    }
}
