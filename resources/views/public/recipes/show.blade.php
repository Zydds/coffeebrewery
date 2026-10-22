@extends('layouts.public')

@section('title', $recipe->title . ' — Coffeebrewery')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('recipes.index') }}"
               class="min-h-[44px] px-6 inline-flex items-center rounded-xl bg-coffee-espresso text-sm font-semibold text-coffee-latte hover:bg-coffee-amber hover:text-coffee-espresso transition-colors">
                Explore Recipes
            </a>
            <a href="{{ route('home') }}"
               class="min-h-[44px] px-6 inline-flex items-center rounded-xl border-2 border-coffee-espresso text-sm font-semibold text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte transition-colors">
                Back to Home
            </a>
        </div>

        <div data-reveal class="mt-6 rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <h1 class="text-3xl font-bold text-coffee-espresso">{{ $recipe->title }}</h1>
                <div class="flex shrink-0 items-center gap-1.5">
                    @if ($recipe->brew_time_min)
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-mocha/15 text-coffee-mocha">
                            {{ $recipe->brew_time_min }} min
                        </span>
                    @endif
                    <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $recipe->cheap
                        ? 'bg-coffee-amber text-coffee-espresso'
                        : 'bg-coffee-mocha/15 text-coffee-mocha' }}">
                        {{ $recipe->cheap ? 'Cheap' : 'Non-cheap' }}
                    </span>
                    @if ($recipe->is_featured)
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-espresso text-coffee-latte">Featured</span>
                    @endif
                </div>
            </div>

            {{-- Pairing --}}
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <a href="{{ $recipe->coffeeBean ? route('beans.show', $recipe->coffeeBean->slug) : route('home').'#bean-catalog' }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-coffee-mocha/40 text-coffee-espresso hover:border-coffee-amber transition-colors">
                    <span class="text-coffee-mocha text-xs">Bean:</span>
                    <span class="font-medium">{{ $recipe->coffeeBean->name ?? 'Unknown' }}</span>
                    @if ($recipe->coffeeBean?->category)
                        <span class="text-xs text-coffee-mocha">({{ $recipe->coffeeBean->category->name }})</span>
                    @endif
                </a>
                <a href="{{ $recipe->brewMethod ? route('methods.show', $recipe->brewMethod->slug) : route('home').'#brew-methods' }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-coffee-mocha/40 text-coffee-espresso hover:border-coffee-amber transition-colors">
                    <span class="text-coffee-mocha text-xs">Method:</span>
                    <span class="font-medium">{{ $recipe->brewMethod->name ?? 'Unknown' }}</span>
                </a>
            </div>

            {{-- Specs --}}
            <dl class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @if ($recipe->brew_time_min)
                    <div class="rounded bg-white border border-coffee-mocha/20 p-4 text-center">
                        <dt class="text-xs uppercase tracking-wider text-coffee-mocha">Total Time</dt>
                        <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->brew_time_min }} min</dd>
                    </div>
                @endif
                <div class="rounded bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">Ratio</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->ratio }}</dd>
                </div>
                <div class="rounded bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">Water Temp</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->water_temp_c }}°C</dd>
                </div>
                <div class="rounded bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">Grind Size</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->grind_size }}</dd>
                </div>
            </dl>

            @php($scale = $recipe->scaleInfo())
            @if ($scale)
                <div class="mt-4 rounded bg-white border border-coffee-mocha/20 p-4 flex flex-wrap items-center justify-between gap-4"
                     x-data="servings({{ $scale['coffee_g'] }}, {{ $scale['water_ml'] }})">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-coffee-mocha">
                            Brew amounts<span x-show="n > 1" x-text="' — ' + n + ' servings'"></span>
                        </p>
                        <p class="mt-1 font-bold text-coffee-espresso">
                            <span x-text="coffee * n"></span>g coffee &rarr; <span x-text="water * n"></span>ml water
                        </p>
                    </div>
                    <div class="flex gap-1.5" role="group" aria-label="Number of servings">
                        <template x-for="i in [1, 2, 3]" :key="i">
                            <button type="button" @click="n = i" :aria-pressed="n === i"
                                    :class="n === i ? 'bg-coffee-espresso text-coffee-latte' : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-mocha/20'"
                                    class="h-10 w-10 rounded-lg text-sm font-semibold transition-colors"
                                    x-text="i + '×'"></button>
                        </template>
                    </div>
                </div>
            @endif

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
    </div>
@endsection
