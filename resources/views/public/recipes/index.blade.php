@extends('layouts.public')

@section('title', __('Recipes — Coffeebrewery'))

@section('meta_description', __('Browse coffee recipes step by step — filter, espresso, French press and more — with ratio, water temperature, and grind size.'))

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-[88px]">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Recipes') }}</h1>

            {{-- Mobile: one snap-scrolling line, so eleven filters never take
                 two rows above the cards. From sm the row wraps as before.
                 The shared dark pill slides between the active chips (app.js
                 + the view-transition rule in app.css). --}}
            <div class="relative flex w-full snap-x snap-mandatory gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden [&>a]:shrink-0 [&>a]:snap-start sm:w-auto sm:flex-wrap sm:overflow-visible sm:pb-0"
                 role="navigation" aria-label="{{ __('Filter recipes') }}"
                 data-filter-pills data-recipe-filters>
                <span data-filter-pill aria-hidden="true"
                      class="pointer-events-none absolute left-0 top-0 rounded-full bg-coffee-espresso"></span>
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

        {{-- Filter/search: the leave handler fades this grid out 120ms, then
             the incoming cards cascade at 50ms. Page links keep the cascade
             off and slide the incoming grid instead (app.js + app.css). --}}
        <div data-recipes-grid class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recipes as $recipe)
                @php
                    // One 46-char ratio wraps into five lines in a quarter-width
                    // stat cell. Keep the first measure in the cell, move the
                    // rest (e.g. "+ 150 ml cold water") to a note line below.
                    $ratio = (string) $recipe->ratio;
                    $ratioNote = null;

                    if (mb_strlen($ratio) > 24 && preg_match('/^(.*?\d+\s*ml)\b(.*)$/iu', $ratio, $m)) {
                        $ratio = trim($m[1]);
                        $ratioNote = trim($m[2]);
                    }
                @endphp

                <a href="{{ route('recipes.show', $recipe->slug) }}"
                   @if (! $paged) x-reveal @endif
                   @if (! $paged) style="--reveal-delay: {{ $loop->index * 50 }}ms" @endif
                   class="recipe-card group flex flex-col rounded-2xl border border-coffee-mocha/30 bg-coffee-cream p-5 hover:border-coffee-amber transition-colors{{ $paged ? '' : ' reveal' }}">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-semibold text-coffee-espresso group-hover:text-coffee-amber-ink transition-colors leading-snug">
                            {{ $recipe->title }}
                        </h2>
                        <div class="flex shrink-0 items-center gap-1.5">
                            {{-- Cheap keeps the filled amber chip; Non-cheap is
                                 an outline only, so cheap recipes read first. --}}
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $recipe->cheap
                                ? 'bg-coffee-amber text-coffee-espresso'
                                : 'border border-coffee-mocha/30 text-coffee-mocha' }}">
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

                    {{-- Thin wave replaces the old flat divider. non-scaling-stroke
                         keeps it 1px at every card width. --}}
                    <svg class="mt-3 h-2 w-full text-coffee-mocha/30" viewBox="0 0 200 8"
                         preserveAspectRatio="none" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                        <path d="M0 4 Q25 1 50 4 T100 4 T150 4 T200 4" vector-effect="non-scaling-stroke"/>
                    </svg>

                    <dl class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-coffee-mocha">
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
                            <dd class="mt-0.5 font-semibold text-coffee-espresso text-sm leading-snug">{{ $ratio }}</dd>
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

                    @if ($ratioNote)
                        <p class="mt-2 text-xs text-coffee-mocha">{{ $ratioNote }}</p>
                    @endif
                </a>
            @empty
                <x-empty-state x-reveal class="reveal"
                               :action="(request('q') || request('method') || request('featured') || request('cheap') || request('quick')) ? __('Clear search & filters') : null"
                               :href="route('recipes.index')">
                    @if (request('q') || request('method') || request('featured') || request('cheap') || request('quick'))
                        {{ __('No recipes found matching your search') }}.
                    @else
                        {{ __('No recipes found yet') }}.
                    @endif
                </x-empty-state>
            @endforelse
        </div>

        <x-page-arrows :paginator="$recipes" data-recipe-pagination />
    </div>
@endsection
