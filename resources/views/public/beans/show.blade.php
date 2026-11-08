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

        {{-- Two-column: media (photo or the card's bean glyph on roast —
             13 of 14 beans ship without an image) | spec sheet panel.
             Stagger cascades media → panel. --}}
        <div data-reveal-stagger class="mt-8 grid gap-6 lg:grid-cols-2 lg:gap-10 items-start">
            <div class="relative aspect-[4/3] overflow-hidden rounded-3xl bg-coffee-roast">
                {{-- Drawn layer first: it is the fallback when the photo
                     fails, so the box is never empty. --}}
                <div class="absolute inset-0 grid place-items-center" role="img" aria-label="{{ $bean->name }}">
                    <svg class="h-28 w-28 text-coffee-amber" viewBox="0 0 48 48" aria-hidden="true">
                        <use href="#cb-{{ $bean->illustration() }}" />
                    </svg>
                </div>

                @if ($bean->image_url)
                    <img src="{{ $bean->image_url }}" alt="{{ $bean->name }}"
                         class="absolute inset-0 h-full w-full object-cover"
                         onerror="this.remove()">
                @endif
            </div>

            <div class="rounded-3xl border border-coffee-mocha/30 bg-coffee-cream p-6 sm:p-8">
                <dl class="divide-y divide-coffee-mocha/20">
                    <div class="flex items-start justify-between gap-4 py-3 first:pt-0">
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
                        <dt class="text-sm text-coffee-mocha">{{ __('Price') }}</dt>
                        <dd class="text-right text-base font-semibold text-coffee-espresso">
                            @if ($bean->price !== null)
                                Rp. {{ number_format((float) $bean->price, 0, ',', '.') }} <span class="font-normal text-coffee-mocha">/ {{ $bean->price_unit ?: '100 gr' }}</span>
                            @else
                                &mdash;
                            @endif
                        </dd>
                    </div>

                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="shrink-0 text-sm text-coffee-mocha">{{ __('Recommended Drinks') }}</dt>
                        <dd class="flex flex-wrap justify-end gap-1.5">
                            @forelse ($bean->recommended_drinks ?? [] as $drink)
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-white border border-coffee-mocha/30 text-coffee-espresso">{{ $drink }}</span>
                            @empty
                                <span class="text-sm text-coffee-mocha/70">{{ __('None listed') }}</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <h2 class="eyebrow">{{ __('Flavor Notes') }}</h2>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @forelse ($bean->flavor_notes ?? [] as $note)
                            <span class="text-sm px-3 py-1 rounded-full bg-coffee-amber/20 text-coffee-espresso border border-coffee-amber/40">{{ $note }}</span>
                        @empty
                            <span class="text-sm text-coffee-mocha/70">{{ __('No flavor notes listed.') }}</span>
                        @endforelse
                    </div>
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
                            <span class="shrink-0 rounded-full border border-coffee-mocha/25 bg-white px-2.5 py-0.5 text-xs font-medium text-coffee-mocha">{{ $recipe->brewMethod?->name }}</span>
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
             layout padding needed. Hidden at lg where the two-column
             layout makes it redundant. --}}
        <div class="sticky bottom-0 z-40 lg:hidden -mx-4 sm:-mx-6 mt-10 flex items-center gap-3 border-t border-coffee-mocha/25 bg-white px-4 sm:px-6 py-3 shadow-[0_-8px_24px_rgba(44,26,20,0.08)]">
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
@endsection
