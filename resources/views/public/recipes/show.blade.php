@extends('layouts.public')

@section('title', $recipe->title . ' — CoffeeHub')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('recipes.index') }}" class="text-sm font-semibold text-coffee-amber hover:underline">
            &larr; Back to recipes
        </a>

        <div class="mt-4 rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <h1 class="text-3xl font-bold text-coffee-espresso">{{ $recipe->title }}</h1>
                @if ($recipe->is_featured)
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-amber text-coffee-espresso">Featured</span>
                @endif
            </div>

            {{-- Pairing --}}
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <a href="{{ $recipe->coffeeBean ? route('beans.show', $recipe->coffeeBean->slug) : route('beans.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-coffee-mocha/40 text-coffee-espresso hover:border-coffee-amber transition-colors">
                    <span class="text-coffee-mocha text-xs">Bean:</span>
                    <span class="font-medium">{{ $recipe->coffeeBean->name ?? 'Unknown' }}</span>
                    @if ($recipe->coffeeBean?->category)
                        <span class="text-xs text-coffee-mocha">({{ $recipe->coffeeBean->category->name }})</span>
                    @endif
                </a>
                <a href="{{ $recipe->brewMethod ? route('methods.show', $recipe->brewMethod->slug) : route('methods.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-coffee-mocha/40 text-coffee-espresso hover:border-coffee-amber transition-colors">
                    <span class="text-coffee-mocha text-xs">Method:</span>
                    <span class="font-medium">{{ $recipe->brewMethod->name ?? 'Unknown' }}</span>
                </a>
            </div>

            {{-- Specs --}}
            <dl class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="rounded bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">Ratio</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->ratio }}</dd>
                </div>
                <div class="rounded bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">Water Temp</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->water_temp_c }}°C</dd>
                </div>
                <div class="rounded bg-white border border-coffee-mocha/20 p-4 text-center col-span-2 sm:col-span-1">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">Grind Size</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->grind_size }}</dd>
                </div>
            </dl>

            {{-- Steps --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-coffee-espresso">Step-by-Step Instructions</h2>
                <ol class="mt-3 list-decimal list-inside space-y-2 text-sm text-coffee-mocha">
                    @foreach ($recipe->step_by_step ?? [] as $step)
                        <li class="pl-1">{{ $step }}</li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="button"
                    @click="window.dispatchEvent(new CustomEvent('open-surprise'))"
                    class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                &#127922; Surprise Me Recipe
            </button>
            <a href="{{ route('recipes.index') }}"
               class="px-4 py-2 rounded border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                Browse More Recipes
            </a>
        </div>
    </div>
@endsection
