@extends('layouts.public')

@section('title', __('Recipes — Coffeebrewery'))

@section('meta_description', __('Browse coffee recipes step by step — filter, espresso, French press and more — with ratio, water temperature, and grind size.'))

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-[88px]">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Recipes') }}</h1>

            <div class="flex flex-wrap gap-2" role="navigation" aria-label="{{ __('Filter recipes') }}">
                <x-chip :href="route('recipes.index', array_filter(['q' => request('q')]))"
                        :active="request('method') === null && !request('featured') && !request('cheap')">
                    {{ __('All') }}
                </x-chip>
                <x-chip :href="route('recipes.index', array_filter(['q' => request('q'), 'method' => request('method'), 'cheap' => request('cheap') ?: null, 'featured' => request('featured') ?: null, 'quick' => 1]))"
                        :active="(bool) request('quick')">
                    {{ __('Quick ≤5 min') }}
                </x-chip>
                <x-chip :href="route('recipes.index', array_filter(['q' => request('q'), 'method' => request('method'), 'featured' => request('featured') ?: null, 'cheap' => 1]))"
                        :active="(bool) request('cheap')">
                    {{ __('Cheap') }}
                </x-chip>
                <x-chip :href="route('recipes.index', array_filter(['q' => request('q'), 'method' => request('method'), 'cheap' => request('cheap') ?: null, 'featured' => 1]))"
                        :active="(bool) request('featured')">
                    {{ __('Featured') }}
                </x-chip>
                @foreach ($methods as $method)
                    <x-chip :href="route('recipes.index', array_filter(['q' => request('q'), 'method' => $method->slug, 'cheap' => request('cheap') ?: null, 'featured' => request('featured') ?: null]))"
                            :active="request('method') === $method->slug">
                        {{ $method->name }}
                    </x-chip>
                @endforeach
            </div>
        </div>

        <x-search-bar :action="route('recipes.index')" :placeholder="__('Search recipes…')" :value="request('q')"
                      :preserves="['method' => request('method'), 'featured' => request('featured'), 'cheap' => request('cheap')]" />

        <div data-reveal-stagger class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recipes as $recipe)
                <a href="{{ route('recipes.show', $recipe->slug) }}"
                   class="group flex flex-col rounded-2xl border border-coffee-mocha/30 bg-coffee-cream p-5 hover:border-coffee-amber transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-semibold text-coffee-espresso group-hover:text-coffee-amber-ink transition-colors leading-snug">
                            {{ $recipe->title }}
                        </h2>
                        <div class="flex shrink-0 items-center gap-1.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $recipe->cheap
                                ? 'bg-coffee-amber text-coffee-espresso'
                                : 'bg-coffee-mocha/15 text-coffee-mocha' }}">
                                {{ $recipe->cheap ? __('Cheap') : __('Non-cheap') }}
                            </span>
                            @if ($recipe->is_featured)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-coffee-espresso text-coffee-latte">{{ __('Featured') }}</span>
                            @endif
                        </div>
                    </div>

                    <p class="mt-2 text-sm text-coffee-mocha">
                        {{ $recipe->coffeeBean->name ?? __('Unknown bean') }} &middot; {{ $recipe->brewMethod->name ?? __('Unknown method') }}
                    </p>

                    <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-coffee-mocha border-t border-coffee-mocha/20 pt-3">
                        <div>
                            <dt>{{ __('Time') }}</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">
                                @if ($recipe->brew_time_min)
                                    {{ $recipe->brew_time_min }} {{ __('min') }}
                                @else
                                    &mdash;
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt>{{ __('Ratio') }}</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">{{ $recipe->ratio }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('Temp') }}</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">{{ $recipe->water_temp_c }}°C</dd>
                        </div>
                        <div>
                            <dt>{{ __('Grind') }}</dt>
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm">{{ $recipe->grind_size }}</dd>
                        </div>
                    </dl>
                </a>
            @empty
                <x-empty-state :action="(request('q') || request('method') || request('featured') || request('cheap') || request('quick')) ? __('Clear search & filters') : null"
                               :href="route('recipes.index')">
                    @if (request('q') || request('method') || request('featured') || request('cheap') || request('quick'))
                        {{ __('No recipes found matching your search') }}.
                    @else
                        {{ __('No recipes found yet') }}.
                    @endif
                </x-empty-state>
            @endforelse
        </div>

        <x-page-arrows :paginator="$recipes" />
    </div>
@endsection
