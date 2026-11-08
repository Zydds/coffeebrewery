<?php

use App\Http\Controllers\Api\CafeMapController;
use App\Http\Controllers\Api\RandomRecipeController;
use App\Http\Controllers\Api\RecipeSuggestController;
use Illuminate\Support\Facades\Route;

Route::get('/cafes', [CafeMapController::class, 'index'])
    ->middleware('throttle:120,1')
    ->name('api.cafes.index');

Route::get('/recipes/random', [RandomRecipeController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('api.recipes.random');

Route::get('/recipes/suggest', [RecipeSuggestController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('api.recipes.suggest');
