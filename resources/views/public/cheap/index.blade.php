@extends('layouts.public')

@section('title', __('Cheap - Coffeebrewery'))

@section('meta_description', __('Great coffee on a budget. Wallet-friendly recipes and cheap bean picks in one place.'))

@section('content')
    {{-- Page header: same card-panel style as the cafe map page.
         pt-6 keeps the gap tight (navbar is sticky, nothing to clear). --}}
    <div class="relative isolate max-w-[1200px] mx-auto px-5 sm:px-8 pt-6 pb-5">
        <div class="rounded-2xl border border-coffee-mocha/40 bg-coffee-cream p-5 sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-coffee-amber text-coffee-espresso shrink-0">
                    {{-- Bean icon --}}
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <ellipse cx="12" cy="12" rx="6" ry="9" transform="rotate(35 12 12)"/>
                        <path d="M8 17c4-2 4-8 8-10"/>
                    </svg>
                </span>
                <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Cheap') }}</h1>
            </div>
            <p class="mt-2 text-coffee-mocha max-w-[560px]">
                {{ __('Sachet and instant coffees, budget-friendly brew methods, and cheap recipes. Drink well for less.') }}
            </p>
        </div>
    </div>

    {{-- Sachet coffee catalog --}}
    <section id="bean-catalog" class="mt-6 scroll-mt-22 max-w-[1200px] mx-auto px-5 sm:px-8">
        <div data-reveal-stagger class="flex flex-wrap items-end justify-between gap-6">
            <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Sachet & Instant Coffee') }}</h2>
            {{-- Same shared dark pill as home: app.js parks it on the active
                 chip, .pill-ready clears that chip's own fill. --}}
            <div class="relative flex flex-wrap gap-2" role="navigation" aria-label="{{ __('Filter sachet coffees by type') }}" data-filter-pills>
                <span data-filter-pill aria-hidden="true"
                      class="pointer-events-none absolute left-0 top-0 rounded-full bg-coffee-espresso"></span>
                <x-chip :href="route('cheap.index', array_filter(['q' => request('q')])).'#bean-catalog'" :active="request('category') === null">
                    {{ __('All') }}
                </x-chip>
                @foreach ($categories as $category)
                    <x-chip :href="route('cheap.index', array_filter(['q' => request('q'), 'category' => $category->slug])).'#bean-catalog'"
                            :active="request('category') === $category->slug">
                        {{ $category->name }}
                    </x-chip>
                @endforeach
            </div>
        </div>

        <x-search-bar :action="route('cheap.index').'#bean-catalog'" :placeholder="__('Search sachet and instant coffees…')" :value="request('q')" :preserves="['category' => request('category')]" />

        {{-- Named for the cross-document view transition: filtering
             cross-fades the results instead of snapping (150ms). Cards
             cascade at 60ms, exactly like home. --}}
        <div class="mt-8 grid grid-cols-2 items-start md:grid-cols-3 lg:grid-cols-4 gap-6 [view-transition-name:bean-results]">
            @forelse ($beans as $bean)
                <x-bean-card :bean="$bean"
                             x-reveal
                             class="reveal"
                             style="--reveal-delay: {{ $loop->index * 60 }}ms" />
            @empty
                <x-empty-state :action="(request('category') || request('q')) ? __('Clear search & filters') : null"
                               :href="route('cheap.index') . '#bean-catalog'">
                    {{ __('No sachet or instant coffees found.') }}
                </x-empty-state>
            @endforelse
        </div>

        <x-page-arrows :paginator="$beans" anchor="bean-catalog" />
    </section>

    {{-- Latte above the wave (the catalog's background), cream below: the
         sachet grid flows into the brew-method band, as on the home page. --}}
    <x-wave-divider from-color="latte" to-color="cream" />

    {{-- Cheap brew methods: cream band, matching the home page --}}
    <div class="bg-coffee-cream">
        <section id="brew-methods" class="relative isolate max-w-[1200px] mx-auto px-5 sm:px-8 section-top pb-22">
            <x-sprig-side side="right" top="28" />
            <h2 data-reveal class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Brew Methods') }}</h2>

            <x-search-bar :action="route('cheap.index').'#brew-methods'" name="method_q" :placeholder="__('Search brew methods…')" :value="request('method_q')" />

            <div class="mt-8 grid grid-cols-2 items-start md:grid-cols-3 lg:grid-cols-4 gap-6 [view-transition-name:method-results]">
                @forelse ($methods as $method)
                    <x-bean-card :method="$method"
                                 x-reveal
                                 class="reveal"
                                 style="--reveal-delay: {{ $loop->index * 60 }}ms" />
                @empty
                    <x-empty-state :action="request('method_q') ? __('Clear search') : null"
                                   :href="route('cheap.index') . '#brew-methods'">
                        {{ __('No brew methods found.') }}
                    </x-empty-state>
                @endforelse
            </div>

            <x-page-arrows :paginator="$methods" anchor="brew-methods" />
        </section>
    </div>

    {{-- The cream band flows into the roast section, same as home. --}}
    <x-wave-divider from-color="cream" to-color="roast" />

    <x-surprise-section cheap-only />
@endsection
