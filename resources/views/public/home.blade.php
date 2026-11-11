@extends('layouts.public')

@section('title', __('Coffeebrewery — Find Your Bean, Nail Your Brew'))

@section('content')
    {{-- Scroll rail: draws down the left gutter as the page scrolls.
         Static in browsers without scroll-driven animations (or with
         reduced motion), so it is never a blank stick. --}}
    <div class="scroll-rail fixed left-8 top-1/2 hidden h-[38vh] w-0.5 -translate-y-1/2 rounded-full bg-coffee-mocha/20 min-[1440px]:block"
         aria-hidden="true">
        <span class="scroll-rail-fill block h-full w-full rounded-full bg-coffee-amber"></span>
    </div>

    {{-- Hero --}}
    <section class="relative isolate bg-coffee-cream">
        {{-- The wave layers belong to the section, not to the cup drawing:
             they span the full width and are clipped by the page wrapper's
             overflow-x: clip, never by the section, so the sprig group can
             sit on the waterline. The SVG is twice as wide as the section so
             the scroll shift can never expose a side edge. --}}
        <div class="pointer-events-none absolute inset-x-0 -bottom-0.5 h-44 sm:h-56 lg:h-64" aria-hidden="true">
            <svg class="h-full w-[200%] -translate-x-1/4" viewBox="0 0 1440 240" preserveAspectRatio="none">
                <g class="wave-drift wave-back">
                    <path d="M0,140 c90,-18 270,18 360,0 c90,-18 270,18 360,0 c90,-18 270,18 360,0 c90,-18 270,18 360,0 L1440,240 L0,240 Z" fill="var(--color-coffee-mocha)" opacity=".12"/>
                </g>
                <g class="wave-drift wave-front">
                    <path d="M0,178 c90,-16 270,16 360,0 c90,-16 270,16 360,0 c90,-16 270,16 360,0 c90,-16 270,16 360,0 L1440,240 L0,240 Z" fill="var(--color-coffee-roast)" opacity=".14"/>
                </g>
            </svg>
        </div>

        <x-sprig-group variant="hero" />

        <div class="relative max-w-[1200px] mx-auto px-5 sm:px-8 py-16 lg:py-[72px] grid gap-10 lg:grid-cols-[1.1fr_.9fr] items-center">
            <div data-reveal-stagger>
                <p class="eyebrow">{{ __('Coffee, explained') }}</p>
                <h1 class="mt-3 font-display font-extrabold tracking-tight text-display">
                    {{ __('Find your bean.') }}
                    <mark class="rounded-xl bg-coffee-amber px-[0.16em] text-coffee-espresso [box-decoration-break:clone] [-webkit-box-decoration-break:clone]">{{ __('Nail your brew.') }}</mark>
                </h1>
                <p class="mt-6 max-w-[480px] text-lg text-coffee-mocha">
                    {{ __('Browse beans by origin, learn how each brew method works, and get a recipe when you cannot decide.') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#surprise"
                       class="group min-h-[52px] px-7 inline-flex items-center gap-2.5 rounded-xl bg-coffee-espresso font-semibold text-coffee-latte transition duration-150 ease-out hover:bg-coffee-amber hover:text-coffee-espresso motion-safe:active:scale-[0.97] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                        <x-dice-icon class="transition-transform duration-150 ease-out motion-safe:group-active:rotate-90" />
                        {{ __('Surprise me') }}
                    </a>
                    <a href="{{ route('recipes.index', ['quick' => 1]) }}"
                       class="min-h-[52px] px-7 inline-flex items-center rounded-xl border-2 border-coffee-espresso font-semibold text-coffee-espresso transition duration-150 ease-out hover:bg-coffee-espresso hover:text-coffee-latte motion-safe:active:scale-[0.97] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                        {{ __('Quick recipes ≤5 min') }}
                    </a>
                </div>
            </div>

            <div data-tilt class="w-full max-w-[460px] justify-self-center">
                <svg class="hero-art" viewBox="0 0 440 480" role="img" aria-label="{{ __('Paper coffee cup surrounded by floating coffee beans') }}">
                    <defs>
                        <symbol id="bean" viewBox="0 0 40 56">
                            <ellipse cx="20" cy="28" rx="17" ry="26" fill="#2C1A14"/>
                            <path d="M21 5c-11 15 9 31-1 46" fill="none" stroke="#6F4E37" stroke-width="3.5" stroke-linecap="round"/>
                        </symbol>
                    </defs>
                    <ellipse cx="225" cy="448" rx="115" ry="14" fill="#2C1A14" opacity=".14"/>
                    <g class="cup-settle">
                        <g transform="rotate(-12 225 270)">
                        <path d="M130 140h190l-26 285q-69 14-138 0z" fill="#FDFBF7"/>
                        <path d="M286 140h34l-26 285q-6 1.5-12 2.5z" fill="#6F4E37" opacity=".14"/>
                        <path class="coffee-fill" d="M139 235h172l-10 112q-76 12-152 0z" fill="#4A2C21"/>
                        <g transform="translate(225 285)" fill="none" stroke="#C87D55" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M-14 -8h22v8a8 8 0 0 1-8 8h-6a8 8 0 0 1-8-8z"/><path d="M8 -6h3a4 4 0 0 1 0 8h-3"/></g>
                        <text x="225" y="326" text-anchor="middle" font-family="Outfit,sans-serif" font-weight="700" font-size="17" fill="#C87D55">Coffeebrewery</text>
                        <path d="M140 118q85-46 170 0z" fill="#6F4E37"/>
                        <rect x="122" y="118" width="206" height="28" rx="14" fill="#6F4E37"/>
                        </g>
                    </g>
                    <g class="bean" style="--i:0"><use href="#bean" x="50" y="150" width="46" height="64" transform="rotate(-30 73 182)"/></g>
                    <g class="bean" style="--i:1"><use href="#bean" x="355" y="120" width="54" height="76" transform="rotate(25 382 158)"/></g>
                    <g class="bean" style="--i:2"><use href="#bean" x="372" y="300" width="44" height="62" transform="rotate(-40 394 331)"/></g>
                    <g class="bean" style="--i:3"><use href="#bean" x="58" y="330" width="40" height="56" transform="rotate(40 78 358)"/></g>
                    <g class="bean" style="--i:4"><use href="#bean" x="190" y="24" width="34" height="48" transform="rotate(70 207 48)"/></g>
                </svg>
            </div>
        </div>
    </section>

    {{-- The divider repeats the hero's beige shoulder (app.css
         .wave-shoulder-hero) and overlaps the layers by 2px, so the beige
         runs under the dark curve with no cream strip or subpixel seam. --}}
    <x-wave-divider from-color="cream" to-color="espresso" class="wave-shoulder-hero relative z-10 -mt-0.5" />

    {{-- Proof strip — full-bleed espresso band; site-wide totals, not the
         filtered paginator counts. Cascade: stats → cafe-map link.
         Numbers count up once on entry (app.js, rAF, 900ms); the
         server-rendered values are the no-JS / reduced-motion state.
         Hairline rules split the stats on desktop only. --}}
    <section class="bg-coffee-espresso text-coffee-cream">
        <div data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 py-8 lg:py-10 flex flex-wrap items-center justify-between gap-x-10 gap-y-6">
            <div class="flex flex-col">
                <span data-count-up="{{ $proof['beans'] }}" class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['beans'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Coffee beans') }}</span>
            </div>
            <span class="hidden h-14 w-px bg-coffee-cream/20 md:block" aria-hidden="true"></span>
            <div class="flex flex-col">
                <span data-count-up="{{ $proof['recipes'] }}" class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['recipes'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Recipes') }}</span>
            </div>
            <span class="hidden h-14 w-px bg-coffee-cream/20 md:block" aria-hidden="true"></span>
            <div class="flex flex-col">
                <span data-count-up="{{ $proof['methods'] }}" class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['methods'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Brew Methods') }}</span>
            </div>

            <a href="{{ route('cafes.index') }}"
               class="group inline-flex min-h-[44px] items-center gap-2 rounded-full border border-coffee-cream/40 px-5 text-sm font-semibold text-coffee-cream transition duration-150 ease-out hover-fine:bg-coffee-cream hover-fine:text-coffee-espresso motion-safe:active:scale-[0.97] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte">
                {{ __('Cafe map') }}
                <svg class="transition-transform duration-150 ease-out motion-safe:group-active:translate-x-[3px]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </a>
        </div>
    </section>

    <x-wave-divider from-color="espresso" to-color="latte" />

    {{-- Bean catalog --}}
    <section id="bean-catalog" class="relative isolate max-w-[1200px] mx-auto px-5 sm:px-8 section-top">
        <div data-reveal-stagger class="flex flex-wrap items-end justify-between gap-6">
            <h2 class="font-display font-bold text-section">{{ __('Bean Catalog') }}</h2>
            <div class="relative flex flex-wrap gap-2" role="navigation" aria-label="{{ __('Filter beans by type') }}" data-filter-pills>
                {{-- Shared dark pill, moved onto the active chip by app.js and
                     named for the cross-document view transition so a filter
                     change slides it instead of swapping backgrounds. --}}
                <span data-filter-pill aria-hidden="true"
                      class="pointer-events-none absolute left-0 top-0 rounded-full bg-coffee-espresso"></span>
                <x-chip :href="route('home', array_filter(['q' => request('q')]))" :active="request('category') === null">
                    {{ __('All') }}
                </x-chip>
                @foreach ($categories as $category)
                    <x-chip :href="route('home', array_filter(['q' => request('q'), 'category' => $category->slug]))"
                            :active="request('category') === $category->slug">
                        {{ $category->name }}
                    </x-chip>
                @endforeach
            </div>
        </div>

        <x-search-bar :action="route('home')" :placeholder="__('Search beans…')" :value="request('q')" :preserves="['category' => request('category')]" />

        @php
            // A full block tiles exactly: the featured 2x2 plus four small
            // cards. Anything after that scrolls in the rail, so no row is
            // ever left with a single orphan. Under five results the block
            // cannot tile, so everything goes to the rail instead.
            $beansOnPage = $featuredBeans->getCollection();
            $blockBeans = $beansOnPage->count() >= 5 ? $beansOnPage->take(5) : collect();
            $railBeans = $beansOnPage->count() >= 5 ? $beansOnPage->slice(5) : $beansOnPage;
            $shownCount = $blockBeans->count() + $railBeans->count();
        @endphp

        {{-- Result count + applied-filter summary --}}
        @if ($featuredBeans->total() > 0 || request()->filled('q') || request()->filled('category'))
            <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-coffee-mocha">
                <span class="font-semibold text-coffee-espresso">
                    {{ __('Showing :shown of :total beans', [
                        'shown' => $shownCount,
                        'total' => $featuredBeans->total(),
                    ]) }}
                </span>
                @if (! request()->filled('q') && ! request()->filled('category') && $cheapBeans > 0)
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('cheap.index') }}"
                       class="font-semibold text-coffee-amber-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink rounded-sm">
                        {{ __(':count more on the Cheap page', ['count' => $cheapBeans]) }}
                    </a>
                @endif
                @if (request('q'))
                    <span aria-hidden="true">·</span>
                    <span>&ldquo;{{ request('q') }}&rdquo;</span>
                @endif
                @if (request('category'))
                    <span aria-hidden="true">·</span>
                    <span>{{ $categories->firstWhere('slug', request('category'))?->name ?? request('category') }}</span>
                @endif
                @if (request()->filled('q') || request()->filled('category'))
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('home') }}"
                       class="font-semibold text-coffee-amber-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink rounded-sm">
                        {{ __('Clear search & filters') }}
                    </a>
                @endif
            </div>
        @endif

        @if ($shownCount === 0)
            <x-empty-state :action="(request('category') || request('q')) ? __('Clear search & filters') : null"
                           :href="route('home') . '#bean-catalog'"
                           class="mt-8 w-full">
                {{ __('No beans found.') }}
            </x-empty-state>
        @else
            {{-- One named wrapper keeps the cross-document transition stable
                 whether the results render as a block, a rail, or both. --}}
            <div class="[view-transition-name:bean-results]">
                {{-- A full block tiles exactly: featured 2 columns x 2 rows
                     plus four small cards. The grid stretches every card in a
                     row to the same resting height. --}}
                @if ($blockBeans->isNotEmpty())
                    <div class="catalog-grid mt-8 grid grid-cols-2 gap-6 lg:grid-cols-4">
                        @foreach ($blockBeans as $bean)
                            <x-bean-card :bean="$bean" :featured="$loop->first"
                                         x-reveal
                                         class="reveal {{ $loop->first ? 'col-span-2 lg:row-span-2' : '' }}"
                                         style="--reveal-delay: {{ $loop->index * 60 }}ms" />
                        @endforeach
                    </div>
                @endif

                {{-- Everything after the block stays on one horizontal line,
                     so a count that does not divide by four can never orphan
                     a card. Arrow buttons are a convenience; the rail still
                     scrolls natively, including by keyboard. --}}
                @if ($railBeans->isNotEmpty())
                    <div class="mt-10" x-data="catalogRail">
                        <div class="flex items-end justify-between gap-4">
                            <h3 class="font-display text-2xl font-bold text-coffee-espresso">{{ __('More beans') }}</h3>
                            <div x-cloak class="flex gap-2">
                                <button type="button" @click="scroll(-1)" :disabled="atStart"
                                        aria-label="{{ __('Scroll beans left') }}"
                                        class="grid h-11 w-11 place-items-center rounded-full border border-coffee-mocha bg-coffee-cream text-coffee-espresso transition-[background-color,color,opacity] duration-150 ease-out hover:bg-coffee-espresso hover:text-coffee-latte disabled:opacity-40 disabled:hover:bg-coffee-cream disabled:hover:text-coffee-espresso focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink motion-safe:active:scale-[0.97]">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button type="button" @click="scroll(1)" :disabled="atEnd"
                                        aria-label="{{ __('Scroll beans right') }}"
                                        class="grid h-11 w-11 place-items-center rounded-full border border-coffee-mocha bg-coffee-cream text-coffee-espresso transition-[background-color,color,opacity] duration-150 ease-out hover:bg-coffee-espresso hover:text-coffee-latte disabled:opacity-40 disabled:hover:bg-coffee-cream disabled:hover:text-coffee-espresso focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink motion-safe:active:scale-[0.97]">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div x-ref="rail"
                             @scroll.debounce.80ms="update()"
                             @resize.window="update()"
                             :tabindex="canScroll ? 0 : -1"
                             class="mt-4 flex snap-x snap-mandatory gap-6 overflow-x-auto pb-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink focus-visible:ring-offset-4 focus-visible:ring-offset-coffee-latte"
                             role="group"
                             aria-label="{{ __('More beans') }}">
                            @foreach ($railBeans as $bean)
                                <x-bean-card :bean="$bean"
                                             x-reveal
                                             class="reveal w-[calc(50%_-_0.75rem)] shrink-0 snap-start sm:w-[300px]"
                                             style="--reveal-delay: {{ $loop->index * 60 }}ms" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <x-page-arrows :paginator="$featuredBeans" />
    </section>

    {{-- Cream band. The divider makes the latte-to-cream change flow: it
         paints latte above the wave (the catalog's background) and cream
         below, continuing into the band. Cards below reuse bean-card, so
         both catalogs share one design. --}}
    <x-wave-divider from-color="latte" to-color="cream" />

    <div class="bg-coffee-cream">
        <x-marquee />

        <section id="brew-methods" class="relative isolate max-w-[1200px] mx-auto px-5 sm:px-8 section-top pb-22">
            <x-sprig-group variant="methods" />
            <h2 data-reveal class="font-display font-bold text-section">{{ __('Brew Methods') }}</h2>

            <x-search-bar :action="route('home')" name="method_q" :placeholder="__('Search brew methods…')" :value="request('method_q')" />

            <div class="mt-8 grid grid-cols-2 items-start md:grid-cols-3 lg:grid-cols-4 gap-6 [view-transition-name:method-results]">
                @forelse ($methods as $method)
                    <x-bean-card :method="$method"
                                 x-reveal
                                 class="reveal"
                                 style="--reveal-delay: {{ $loop->index * 60 }}ms" />
                @empty
                    <x-empty-state :action="request('method_q') ? __('Clear search') : null"
                                   :href="route('home') . '#brew-methods'">
                        {{ __('No brew methods found.') }}
                    </x-empty-state>
                @endforelse
            </div>

            <x-page-arrows :paginator="$methods" />
        </section>
    </div>

    {{-- The cream band flows into the roast surprise section: cream above
         the wave (the band's own background), roast below, continuing
         into the section. --}}
    <x-wave-divider from-color="cream" to-color="roast" />

    <x-surprise-section :sprig="true" />
@endsection
