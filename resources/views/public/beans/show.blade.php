@extends('layouts.public')

@section('title', $bean->name . ' — Coffeebrewery')

@section('meta_description', __(':name beans from :origin — flavor notes, acidity and body, price, and the recipes that use them.', [
    'name' => $bean->name,
    'origin' => $bean->origin_country ?: __('Unknown'),
]))

@section('og_image', $bean->image_url ?? '')

@section('content')
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('home') }}#bean-catalog" class="text-sm font-semibold text-coffee-amber-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink rounded-sm">
            &larr; {{ __('All beans') }}
        </a>

        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2">
            <h1 class="font-display text-[clamp(2rem,4vw,3rem)] font-extrabold leading-[1.1] tracking-tight text-coffee-espresso">{{ $bean->name }}</h1>
            <span class="text-sm font-medium px-3 py-1 rounded-full bg-coffee-mocha/15 text-coffee-mocha">
                {{ $bean->category->name ?? __('Uncategorized') }}
            </span>
        </div>

        {{-- Flavor line: one readable sentence above the map, the way a lot
             sheet lists cup notes before the origin. --}}
        <div class="mt-5 flex items-start gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-coffee-espresso text-coffee-amber">
                <svg class="h-5 w-5" viewBox="0 0 48 48" aria-hidden="true">
                    <use href="#cb-{{ $bean->illustration() }}" />
                </svg>
            </span>
            <p class="pt-2 text-base font-medium leading-relaxed text-coffee-espresso">
                @if (! empty($bean->flavor_notes))
                    {{ implode(', ', $bean->flavor_notes) }}
                @else
                    <span class="text-coffee-mocha/70">{{ __('No flavor notes listed.') }}</span>
                @endif
            </p>
        </div>

        <div data-reveal-stagger class="mt-8 grid items-start gap-6 lg:grid-cols-12 lg:gap-8">
            {{-- Origin map. Leaflet paints its panes at z-index 400-800, so
                 the frame isolates them (same reasoning as the cafe map). --}}
            <div class="lg:col-span-7">
                <div class="relative isolate overflow-hidden rounded-3xl border border-coffee-mocha/40 bg-coffee-cream">
                    @if ($origin)
                        <div id="bean-origin-map"
                             class="h-[300px] w-full sm:h-[380px] lg:h-[440px]"
                             role="region"
                             aria-label="{{ __('Map showing where :name is grown: :origin', ['name' => $bean->name, 'origin' => $bean->origin_country]) }}"></div>

                        <p class="pointer-events-none absolute right-4 top-4 z-[500] max-w-[calc(100%-2rem)] truncate rounded-lg bg-coffee-espresso px-3 py-1.5 text-sm font-semibold text-coffee-latte shadow-[0_4px_12px_rgba(44,26,20,0.25)]">
                            {{ $bean->name }}
                        </p>
                    @else
                        {{-- No coordinates: keep the block honest with text
                             rather than centring a map on empty ocean. --}}
                        <div class="flex h-[300px] flex-col items-center justify-center gap-3 p-6 text-center sm:h-[380px] lg:h-[440px]">
                            <svg class="h-9 w-9 text-coffee-amber" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/>
                                <circle cx="12" cy="10" r="2.5"/>
                            </svg>
                            <p class="font-display text-2xl font-bold text-coffee-espresso">{{ $bean->origin_country ?: __('Unknown origin') }}</p>
                            <p class="text-sm text-coffee-mocha">{{ __('No map for this origin yet.') }}</p>
                        </div>
                    @endif
                </div>

                @if ($origin)
                    <p class="mt-3 text-sm text-coffee-mocha">{{ __('Map data © OpenStreetMap contributors © CARTO') }}</p>
                @endif
            </div>

            {{-- Spec sheet: price, origin and cup profile in one panel, then
                 the one action the site can actually fulfil — the recipes. --}}
            <div class="lg:col-span-5">
                <div class="rounded-3xl border border-coffee-mocha/30 bg-coffee-cream p-6 sm:p-8">
                    <span class="eyebrow">{{ __('Single origin') }}</span>

                    <p class="mt-2 font-display text-3xl font-bold leading-tight text-coffee-espresso tabular-nums">
                        @if ($bean->price !== null)
                            Rp. {{ number_format((float) $bean->price, 0, ',', '.') }}
                            <span class="text-sm font-medium text-coffee-mocha">/ {{ $bean->price_unit ?: '100 gr' }}</span>
                        @else
                            <span class="text-xl">{{ __('Price on request') }}</span>
                        @endif
                    </p>

                    <dl class="mt-5 divide-y divide-coffee-mocha/20">
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-coffee-mocha">{{ __('Origin') }}</dt>
                            <dd class="text-right text-base font-semibold text-coffee-espresso">{{ $bean->origin_country ?: '—' }}</dd>
                        </div>

                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-coffee-mocha">{{ __('Acidity') }}</dt>
                            <dd class="flex items-center gap-3" role="img" aria-label="{{ __('Acidity :level of 5', ['level' => $bean->acidity_level]) }}">
                                <span class="flex gap-1.5" aria-hidden="true">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="h-3 w-3 rounded-full border-2 border-coffee-mocha {{ $i <= $bean->acidity_level ? 'border-coffee-espresso bg-coffee-espresso' : '' }}"></i>
                                    @endfor
                                </span>
                                <span class="font-display text-lg font-bold text-coffee-espresso tabular-nums">{{ $bean->acidity_level }}<span class="text-sm font-medium text-coffee-mocha">/5</span></span>
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-coffee-mocha">{{ __('Body') }}</dt>
                            <dd class="flex items-center gap-3" role="img" aria-label="{{ __('Body :level of 5', ['level' => $bean->body_level]) }}">
                                <span class="flex gap-1.5" aria-hidden="true">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="h-3 w-3 rounded-full border-2 border-coffee-mocha {{ $i <= $bean->body_level ? 'border-coffee-espresso bg-coffee-espresso' : '' }}"></i>
                                    @endfor
                                </span>
                                <span class="font-display text-lg font-bold text-coffee-espresso tabular-nums">{{ $bean->body_level }}<span class="text-sm font-medium text-coffee-mocha">/5</span></span>
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="shrink-0 text-sm text-coffee-mocha">{{ __('Best brewed with') }}</dt>
                            <dd class="flex flex-wrap justify-end gap-1.5">
                                @forelse ($bean->recommended_methods ?? [] as $slug)
                                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-coffee-amber/20 border border-coffee-amber/40 text-coffee-espresso">{{ $brewMethods[$slug] ?? $slug }}</span>
                                @empty
                                    <span class="text-sm text-coffee-mocha/70">{{ __('None listed') }}</span>
                                @endforelse
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="shrink-0 text-sm text-coffee-mocha">{{ __('Recommended Drinks') }}</dt>
                            <dd class="flex flex-wrap justify-end gap-1.5">
                                @forelse ($bean->recommended_drinks ?? [] as $drink)
                                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-coffee-latte border border-coffee-mocha/30 text-coffee-espresso">{{ $drink }}</span>
                                @empty
                                    <span class="text-sm text-coffee-mocha/70">{{ __('None listed') }}</span>
                                @endforelse
                            </dd>
                        </div>
                    </dl>

                    <a href="#bean-recipes"
                       class="mt-6 flex min-h-[52px] w-full items-center justify-center rounded-xl bg-coffee-espresso px-6 font-semibold text-coffee-latte transition-colors duration-150 ease-out hover:bg-coffee-amber hover:text-coffee-espresso motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                        {{ __('See recipes') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Recipes with this bean — replaces the lone Browse CTA with the
             actual relational content (CoffeeBean::recipes). --}}
        <section id="bean-recipes" class="mt-12 scroll-mt-24">
            <h2 data-reveal class="font-display text-[clamp(1.75rem,3vw,2.25rem)] font-bold leading-tight text-coffee-espresso">{{ __('Recipes with this bean') }}</h2>

            <div data-reveal-stagger class="mt-6 grid gap-4 sm:grid-cols-2">
                @forelse ($bean->recipes as $recipe)
                    <a href="{{ route('recipes.show', $recipe->slug) }}"
                       class="group flex flex-col rounded-2xl border border-coffee-mocha/30 bg-coffee-cream p-5 transition-colors duration-150 ease-out hover:border-coffee-amber focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-semibold leading-snug text-coffee-espresso transition-colors duration-150 group-hover:text-coffee-amber-ink">{{ $recipe->title }}</h3>
                            <span class="shrink-0 rounded-full border border-coffee-mocha/25 bg-coffee-latte px-2.5 py-0.5 text-xs font-medium text-coffee-mocha">{{ $recipe->brewMethod?->name }}</span>
                        </div>
                        <dl class="mt-4 grid grid-cols-3 gap-3">
                            <div>
                                <dt class="text-xs text-coffee-mocha">{{ __('Ratio') }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-coffee-espresso">{{ $recipe->ratio ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-coffee-mocha">{{ __('Temp') }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-coffee-espresso">{{ $recipe->water_temp_c ?? '—' }}{{ $recipe->water_temp_c !== null ? '°C' : '' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-coffee-mocha">{{ __('Time') }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-coffee-espresso">{{ $recipe->brew_time_min ? $recipe->brew_time_min.' '.__('min') : '—' }}</dd>
                            </div>
                        </dl>
                    </a>
                @empty
                    <x-empty-state :action="__('Browse Recipes')" :href="route('recipes.index')">
                        {{ __('No recipes yet. Check back soon.') }}
                    </x-empty-state>
                @endforelse
            </div>
        </section>

        {{-- Mobile action bar — position:sticky pins it to the viewport
             bottom while the page scrolls, then releases at its flow
             position (content end) so it never covers the footer; no
             layout padding needed. Hidden at lg where the spec panel's
             CTA is already in view. --}}
        <div class="sticky bottom-0 z-40 lg:hidden -mx-4 sm:-mx-6 mt-10 flex items-center gap-3 border-t border-coffee-mocha/25 bg-coffee-latte px-4 sm:px-6 py-3 shadow-[0_-8px_24px_rgba(44,26,20,0.08)]">
            <div class="min-w-0 flex-1">
                @if ($bean->price !== null)
                    <p class="truncate text-sm font-semibold text-coffee-espresso">
                        Rp. {{ number_format((float) $bean->price, 0, ',', '.') }}
                        <span class="font-normal text-coffee-mocha">/ {{ $bean->price_unit ?: '100 gr' }}</span>
                    </p>
                @else
                    <p class="truncate text-sm font-semibold text-coffee-espresso">{{ $bean->name }}</p>
                @endif
            </div>
            <a href="#bean-recipes"
               class="shrink-0 min-h-[44px] px-5 inline-flex items-center rounded-xl bg-coffee-espresso text-sm font-semibold text-coffee-latte transition-colors duration-150 ease-out hover:bg-coffee-amber hover:text-coffee-espresso motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                {{ __('See recipes') }}
            </a>
        </div>
    </div>

    @if ($origin)
        <script>
            window.BEAN_ORIGIN = @json($origin);
        </script>

        @vite('resources/js/bean-map.js')
    @endif
@endsection
