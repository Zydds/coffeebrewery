@extends('layouts.public')

@section('title', 'Recipes — Coffeebrewery')

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-[88px]">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">Recipes</h1>

            <div class="flex flex-wrap gap-2" role="navigation" aria-label="Filter recipes">
                <a href="{{ route('recipes.index', array_filter(['q' => request('q')])) }}"
                   class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('method') === null && !request('featured') && !request('cheap')
                        ? 'bg-coffee-espresso text-coffee-latte'
                        : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                    All
                </a>
                <a href="{{ route('recipes.index', array_filter(['q' => request('q'), 'method' => request('method'), 'cheap' => request('cheap') ?: null, 'featured' => request('featured') ?: null, 'quick' => 1])) }}"
                   class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('quick')
                        ? 'bg-coffee-espresso text-coffee-latte'
                        : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                    Quick &le;5 min
                </a>
                <a href="{{ route('recipes.index', array_filter(['q' => request('q'), 'method' => request('method'), 'featured' => request('featured') ?: null, 'cheap' => 1])) }}"
                   class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('cheap')
                        ? 'bg-coffee-espresso text-coffee-latte'
                        : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                    Cheap
                </a>
                <a href="{{ route('recipes.index', array_filter(['q' => request('q'), 'method' => request('method'), 'cheap' => request('cheap') ?: null, 'featured' => 1])) }}"
                   class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('featured')
                        ? 'bg-coffee-espresso text-coffee-latte'
                        : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                    Featured
                </a>
                @foreach ($methods as $method)
                    <a href="{{ route('recipes.index', array_filter(['q' => request('q'), 'method' => $method->slug, 'cheap' => request('cheap') ?: null, 'featured' => request('featured') ?: null])) }}"
                       class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('method') === $method->slug
                            ? 'bg-coffee-espresso text-coffee-latte'
                            : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                        {{ $method->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <x-search-bar :action="route('recipes.index')" placeholder="Search recipes…" :value="request('q')"
                      :preserves="['method' => request('method'), 'featured' => request('featured'), 'cheap' => request('cheap')]" />

        <div data-reveal class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recipes as $recipe)
                <a href="{{ route('recipes.show', $recipe->slug) }}"
                   class="group flex flex-col rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5 hover:border-coffee-amber transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-semibold text-coffee-espresso group-hover:text-coffee-amber transition-colors leading-snug">
                            {{ $recipe->title }}
                        </h2>
                        <div class="flex shrink-0 items-center gap-1.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $recipe->cheap
                                ? 'bg-coffee-amber text-coffee-espresso'
                                : 'bg-coffee-mocha/15 text-coffee-mocha' }}">
                                {{ $recipe->cheap ? 'Cheap' : 'Non-cheap' }}
                            </span>
                            @if ($recipe->is_featured)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-coffee-espresso text-coffee-latte">Featured</span>
                            @endif
                        </div>
                    </div>

                    <p class="mt-2 text-sm text-coffee-mocha">
                        {{ $recipe->coffeeBean->name ?? 'Unknown bean' }} &middot; {{ $recipe->brewMethod->name ?? 'Unknown method' }}
                    </p>

                    <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-coffee-mocha border-t border-coffee-mocha/20 pt-3">
                        <div>
                            <dt>Time</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">
                                @if ($recipe->brew_time_min)
                                    {{ $recipe->brew_time_min }} min
                                @else
                                    &mdash;
                                @endif
                            </dd>
                        </div>
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
                    <p class="text-sm text-coffee-mocha">No recipes found{{ request('q') || request('method') || request('featured') || request('cheap') || request('quick') ? ' matching your search' : ' yet' }}.</p>
                    @if (request('q') || request('method') || request('featured') || request('cheap') || request('quick'))
                        <a href="{{ route('recipes.index') }}" class="mt-3 inline-block text-sm font-semibold text-coffee-amber hover:underline">
                            Clear search &amp; filters
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <x-page-arrows :paginator="$recipes" />
    </div>
@endsection
