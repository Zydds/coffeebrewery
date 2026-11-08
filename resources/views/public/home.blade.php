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
    <section class="bg-coffee-cream">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-16 lg:py-[72px] grid gap-10 lg:grid-cols-[1.1fr_.9fr] items-center">
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
                    {{-- Two wave bands drift behind the cup — the home
                         page's wave layer, now scroll-linked (app.css
                         maps the shift); browsers without scroll timelines
                         get a static drawing. Palette tones only. --}}
                    <g class="wave-drift wave-back">
                        <path d="M0,368 c55,-14 165,14 220,0 c55,-14 165,14 220,0 c55,-14 165,14 220,0 c55,-14 165,14 220,0 L880,480 L0,480 Z" fill="#6F4E37" opacity=".12"/>
                    </g>
                    <g class="wave-drift wave-front">
                        <path d="M0,404 c55,-12 165,12 220,0 c55,-12 165,12 220,0 c55,-12 165,12 220,0 c55,-12 165,12 220,0 L880,480 L0,480 Z" fill="#4A2C21" opacity=".14"/>
                    </g>
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

    <x-wave-divider from-color="cream" to-color="espresso" />

    {{-- Proof strip — full-bleed espresso band; site-wide totals, not the
         filtered paginator counts. Cascade: stats → cafe-map link.
         Numbers count up once on entry (app.js, rAF, 900ms); the
         server-rendered values are the no-JS / reduced-motion state.
         Thin wave rules split the stats on desktop only. --}}
    <section class="bg-coffee-espresso text-coffee-cream">
        <div data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 py-8 lg:py-10 flex flex-wrap items-center justify-between gap-x-10 gap-y-6">
            <div class="flex flex-col">
                <span data-count-up="{{ $proof['beans'] }}" class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['beans'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Coffee beans') }}</span>
            </div>
            <svg class="hidden md:block h-14 w-4 text-coffee-cream/25" viewBox="0 0 16 56" fill="none" aria-hidden="true">
                <path d="M8 2q-6 6 0 12t0 12 0 12 0 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <div class="flex flex-col">
                <span data-count-up="{{ $proof['recipes'] }}" class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['recipes'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Recipes') }}</span>
            </div>
            <svg class="hidden md:block h-14 w-4 text-coffee-cream/25" viewBox="0 0 16 56" fill="none" aria-hidden="true">
                <path d="M8 2q-6 6 0 12t0 12 0 12 0 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
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
    <section id="bean-catalog" class="max-w-[1200px] mx-auto px-5 sm:px-8 section-top">
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

        {{-- Result count + applied-filter summary --}}
        @if ($featuredBeans->total() > 0 || request()->filled('q') || request()->filled('category'))
            <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-coffee-mocha">
                <span class="font-semibold text-coffee-espresso">
                    {{ __('Showing :from–:to of :total beans', [
                        'from' => $featuredBeans->firstItem() ?? 0,
                        'to' => $featuredBeans->lastItem() ?? 0,
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

        {{-- Mobile: a snap carousel two cards wide; from md all six fit in a
             grid, so no scroll and no edge fade. The grid is a named view
             transition element — filtering cross-fades results (150ms). --}}
        <div class="-mx-5 mt-8 flex snap-x snap-mandatory items-start gap-6 overflow-x-auto scroll-pl-5 px-5 pb-2 sm:-mx-8 sm:px-8 sm:scroll-pl-8 md:mx-0 md:grid md:grid-cols-3 md:gap-6 md:overflow-visible md:px-0 md:pb-0 md:scroll-pl-0 lg:grid-cols-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink focus-visible:ring-offset-4 focus-visible:ring-offset-coffee-latte [view-transition-name:bean-results]"
             data-scroll-fade
             role="group"
             aria-label="{{ __('Featured beans') }}">
            @forelse ($featuredBeans as $bean)
                @php($isFeatured = $loop->first && $featuredBeans->count() > 1)
                <x-bean-card :bean="$bean" :featured="$isFeatured"
                             x-reveal
                             class="reveal shrink-0 snap-start {{ $isFeatured ? 'w-full md:col-span-2 md:row-span-2' : 'w-[calc(50%_-_0.75rem)] md:w-auto' }}"
                             style="--reveal-delay: {{ $loop->index * 60 }}ms" />
            @empty
                <x-empty-state :action="(request('category') || request('q')) ? __('Clear search & filters') : null"
                               :href="route('home') . '#bean-catalog'"
                               class="w-full">
                    {{ __('No beans found.') }}
                </x-empty-state>
            @endforelse
        </div>

        <x-page-arrows :paginator="$featuredBeans" />
    </section>

    {{-- Cream band. The divider makes the latte-to-cream change flow: it
         paints latte above the wave (the catalog's background) and cream
         below, continuing into the band. Cards below reuse bean-card, so
         both catalogs share one design. --}}
    <x-wave-divider from-color="latte" to-color="cream" />

    <div class="bg-coffee-cream">
        <x-marquee />

        <section id="brew-methods" class="max-w-[1200px] mx-auto px-5 sm:px-8 section-top pb-22">
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

    <x-surprise-section />
@endsection
