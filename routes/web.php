<?php

use App\Http\Controllers\Admin\BeanController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MethodController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Api\RandomRecipeController;
use App\Http\Controllers\Api\RecipeSuggestController;
use App\Http\Controllers\BrewFinderController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipePublicController;
use Illuminate\Support\Facades\Route;

// ── Public routes ──────────────────────────────────────────
Route::get('/', [CatalogController::class, 'welcome'])->name('home');
Route::get('/cheap', [CatalogController::class, 'cheapIndex'])->name('cheap.index');
Route::get('/custom', [BrewFinderController::class, 'index'])->name('custom.index');
Route::get('/beans/{slug}', [CatalogController::class, 'beanShow'])->name('beans.show');
Route::get('/recipes', [RecipePublicController::class, 'index'])->name('recipes.index');
Route::get('/recipes/random', [RecipePublicController::class, 'random'])->name('recipes.random');
Route::get('/recipes/{slug}', [RecipePublicController::class, 'show'])->name('recipes.show');
Route::get('/methods/{slug}', [CatalogController::class, 'methodShow'])->name('methods.show');

// ── API routes ─────────────────────────────────────────────
Route::get('/api/recipes/random', [RandomRecipeController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('api.recipes.random');
Route::get('/api/recipes/suggest', [RecipeSuggestController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('api.recipes.suggest');

// ── Admin routes ───────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('beans', BeanController::class)->except(['show']);
        Route::resource('methods', MethodController::class)->except(['show']);
        Route::resource('recipes', RecipeController::class)->except(['show']);
    });
});

// ── Profile routes (authenticated users) ───────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
