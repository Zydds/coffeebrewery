@extends('layouts.public')

@section('title', 'Recipes — CoffeeHub')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-coffee-espresso">Recipes</h1>
                <p class="mt-2 text-sm text-coffee-mocha">Bean and method pairings with exact ratios, temperatures and steps.</p>
            </div>
            <button type="button"
                    @click="window.dispatchEvent(new CustomEvent('open-surprise'))"
                    class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                &#127922; Surprise Me Recipe
            </button>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recipes as $recipe)
                <a href="{{ route('recipes.show', $recipe->slug) }}"
                   class="group flex flex-col rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5 hover:border-coffee-amber transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-semibold text-coffee-espresso group-hover:text-coffee-amber transition-colors leading-snug">
                            {{ $recipe->title }}
                        </h2>
                        @if ($recipe->is_featured)
                            <span class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full bg-coffee-amber text-coffee-espresso">Featured</span>
                        @endif
                    </div>

                    <p class="mt-2 text-sm text-coffee-mocha">
                        {{ $recipe->coffeeBean->name ?? 'Unknown bean' }} &middot; {{ $recipe->brewMethod->name ?? 'Unknown method' }}
                    </p>

                    <dl class="mt-4 grid grid-cols-3 gap-2 text-xs text-coffee-mocha border-t border-coffee-mocha/20 pt-3">
                        <div>
                            <dt>Ratio</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">{{ $recipe->ratio }}</dd>
                        </div>
                        <div>
                            <dt>Temp</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">{{ $recipe->water_temp_c }}°C</dd>
                        </div>
                        <div>
                            <dt>Grind</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">{{ $recipe->grind_size }}</dd>
                        </div>
                    </dl>
                </a>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-coffee-mocha/40 p-10 text-center">
                    <p class="text-sm text-coffee-mocha">No recipes yet.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
