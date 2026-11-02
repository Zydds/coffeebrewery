@extends('layouts.public')

@section('title', __('Coffeebrewery — Find Your Bean, Nail Your Brew'))

@section('content')
    {{-- Hero --}}
    <section class="bg-coffee-cream">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-16 lg:py-[72px] grid gap-10 lg:grid-cols-[1.1fr_.9fr] items-center">
            <div data-reveal-stagger>
                <p class="eyebrow">{{ __('Coffee, explained') }}</p>
                <h1 class="mt-3 font-display font-extrabold leading-[1.1] tracking-tight text-[clamp(2.6rem,6vw,4.5rem)]">
                    {{ __('Find your bean.') }}
                    <mark class="rounded-xl bg-coffee-amber px-[0.16em] text-coffee-espresso [box-decoration-break:clone] [-webkit-box-decoration-break:clone]">{{ __('Nail your brew.') }}</mark>
                </h1>
                <p class="mt-6 max-w-[480px] text-lg text-coffee-mocha">
                    {{ __('Browse beans by origin, learn how each brew method works, and get a recipe when you cannot decide.') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#surprise"
                       class="min-h-[52px] px-7 inline-flex items-center gap-2.5 rounded-xl bg-coffee-espresso font-semibold text-coffee-latte transition-colors duration-150 ease-out hover:bg-coffee-amber hover:text-coffee-espresso motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                        <x-dice-icon />
                        {{ __('Surprise me') }}
                    </a>
                    <a href="{{ route('recipes.index', ['quick' => 1]) }}"
                       class="min-h-[52px] px-7 inline-flex items-center rounded-xl border-2 border-coffee-espresso font-semibold text-coffee-espresso transition-colors duration-150 ease-out hover:bg-coffee-espresso hover:text-coffee-latte motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                        {{ __('Quick recipes ≤5 min') }}
                    </a>
                </div>
            </div>

            <div data-reveal class="w-full max-w-[460px] justify-self-center">
                <svg class="hero-art" viewBox="0 0 440 480" role="img" aria-label="{{ __('Paper coffee cup surrounded by floating coffee beans') }}">
                    <defs>
                        <symbol id="bean" viewBox="0 0 40 56">
                            <ellipse cx="20" cy="28" rx="17" ry="26" fill="#2C1A14"/>
                            <path d="M21 5c-11 15 9 31-1 46" fill="none" stroke="#6F4E37" stroke-width="3.5" stroke-linecap="round"/>
                        </symbol>
                    </defs>
                    <ellipse cx="225" cy="448" rx="115" ry="14" fill="#2C1A14" opacity=".14"/>
                    <g class="cup-float">
                        <g transform="rotate(-12 225 270)">
                        <path d="M130 140h190l-26 285q-69 14-138 0z" fill="#FDFBF7"/>
                        <path d="M286 140h34l-26 285q-6 1.5-12 2.5z" fill="#6F4E37" opacity=".14"/>
                        <path d="M139 235h172l-10 112q-76 12-152 0z" fill="#4A2C21"/>
                        <g transform="translate(225 285)" fill="none" stroke="#C87D55" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M-14 -8h22v8a8 8 0 0 1-8 8h-6a8 8 0 0 1-8-8z"/><path d="M8 -6h3a4 4 0 0 1 0 8h-3"/></g>
                        <text x="225" y="326" text-anchor="middle" font-family="Outfit,sans-serif" font-weight="700" font-size="17" fill="#C87D55">Coffeebrewery</text>
                        <path d="M140 118q85-46 170 0z" fill="#6F4E37"/>
                        <rect x="122" y="118" width="206" height="28" rx="14" fill="#6F4E37"/>
                        </g>
                    </g>
                    <g class="fl"><use href="#bean" x="50" y="150" width="46" height="64" transform="rotate(-30 73 182)"/></g>
                    <g class="fl" style="animation-delay:-2s"><use href="#bean" x="355" y="120" width="54" height="76" transform="rotate(25 382 158)"/></g>
                    <g class="fl" style="animation-delay:-4s"><use href="#bean" x="372" y="300" width="44" height="62" transform="rotate(-40 394 331)"/></g>
                    <g class="fl" style="animation-delay:-1s"><use href="#bean" x="58" y="330" width="40" height="56" transform="rotate(40 78 358)"/></g>
                    <g class="fl" style="animation-delay:-3s"><use href="#bean" x="190" y="24" width="34" height="48" transform="rotate(70 207 48)"/></g>
                </svg>
            </div>
        </div>
    </section>

    {{-- Proof strip — full-bleed espresso band; site-wide totals, not the
         filtered paginator counts. Cascade: stats → cafe-map link. --}}
    <section class="bg-coffee-espresso text-coffee-cream">
        <div data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 py-8 lg:py-10 flex flex-wrap items-center justify-between gap-x-10 gap-y-6">
            <div class="flex flex-col">
                <span class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['beans'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Coffee beans') }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['recipes'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Recipes') }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-display text-3xl lg:text-4xl font-extrabold leading-none text-coffee-latte tabular-nums">{{ $proof['methods'] }}</span>
                <span class="mt-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Brew Methods') }}</span>
            </div>

            <a href="{{ route('cafes.index') }}"
               class="inline-flex min-h-[44px] items-center gap-2 rounded-full border border-coffee-cream/40 px-5 text-sm font-semibold text-coffee-cream transition-colors duration-150 ease-out hover-fine:bg-coffee-cream hover-fine:text-coffee-espresso motion-safe:active:scale-[0.97] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte">
                {{ __('Cafe map') }}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </a>
        </div>
    </section>

    {{-- Bean catalog --}}
    <section id="bean-catalog" data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 section-top">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Bean Catalog') }}</h2>
            <div class="flex flex-wrap gap-2" role="navigation" aria-label="{{ __('Filter beans by type') }}">
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
                    {{ __('Showing :from–:to of :total', [
                        'from' => $featuredBeans->firstItem() ?? 0,
                        'to' => $featuredBeans->lastItem() ?? 0,
                        'total' => $featuredBeans->total(),
                    ]) }}
                </span>
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

        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($featuredBeans as $bean)
                <x-bean-card :bean="$bean" />
            @empty
                <x-empty-state :action="(request('category') || request('q')) ? __('Clear search & filters') : null"
                               :href="route('home') . '#bean-catalog'">
                    {{ __('No beans found.') }}
                </x-empty-state>
            @endforelse
        </div>

        <x-page-arrows :paginator="$featuredBeans" />
    </section>

    {{-- Brew methods — full-bleed cream band; white method-cards pop
         against it (bean-cards stay cream on the latte catalog above). --}}
    <div class="bg-coffee-cream">
        <section id="brew-methods" data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 section-top pb-22">
            <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Brew Methods') }}</h2>

            <x-search-bar :action="route('home')" name="method_q" :placeholder="__('Search brew methods…')" :value="request('method_q')" />

            <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($methods as $method)
                    <x-method-card :method="$method" />
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

    {{-- Surprise me — flush against the cream band (no gap); the two
         columns cascade copy → card. --}}
    <section id="surprise" data-reveal-stagger class="bg-coffee-roast py-[88px] text-coffee-cream" x-data="surpriseSection()">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 grid gap-10 lg:grid-cols-[.85fr_1.15fr] items-center">
            <div>
                <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)] text-coffee-latte">{{ __('Can\'t decide what to brew?') }}</h2>
                <p class="mt-4 max-w-[420px]">{{ __('Roll a random recipe. Every roll gives you a different one.') }}</p>
                <button type="button"
                        @click="rollNext()"
                        :disabled="loading"
                        class="mt-8 min-h-[52px] px-7 inline-flex items-center gap-2.5 rounded-xl bg-coffee-amber font-semibold text-coffee-espresso transition-colors duration-150 ease-out hover:bg-coffee-latte motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte disabled:opacity-60">
                    <x-dice-icon :size="22" />
                    <span x-text="rolled ? {{ \Illuminate\Support\Js::from(__('Roll another')) }} : {{ \Illuminate\Support\Js::from(__('Surprise me')) }}"></span>
                </button>

                <div class="mt-4 flex flex-col items-start gap-2">
                    <label class="inline-flex items-center gap-2 text-sm text-coffee-cream cursor-pointer select-none">
                        <input type="checkbox" x-model="cheap" @change="roll()"
                               class="h-4 w-4 rounded-sm border-coffee-latte/40 bg-transparent text-coffee-amber focus:ring-coffee-amber">
                        {{ __('Cheap only') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-coffee-cream cursor-pointer select-none">
                        <input type="checkbox" x-model="quick" @change="roll()"
                               class="h-4 w-4 rounded-sm border-coffee-latte/40 bg-transparent text-coffee-amber focus:ring-coffee-amber">
                        {{ __('Quick only (≤5 min)') }}
                    </label>
                </div>
            </div>

            <article class="flex flex-col gap-5 rounded-3xl bg-coffee-latte p-6 sm:p-8 text-coffee-espresso" aria-live="polite">
                <p x-show="loading" x-cloak class="text-sm text-coffee-mocha">{{ __('Brewing a suggestion…') }}</p>

                <p x-show="error" x-cloak class="text-sm text-red-700">
                    <span x-text="error"></span>
                    <button type="button" @click="roll(recipe?.id ?? null)" class="font-semibold underline">{{ __('Try again') }}</button>
                </p>

                <p x-show="empty && !loading && !error" x-cloak class="text-sm text-coffee-mocha">
                    {{ __('No recipes yet. Check back soon.') }}
                </p>

                <div x-ref="card" x-show="recipe && !loading && !error" x-cloak>
                    <h3 class="font-display text-2xl font-bold text-coffee-espresso" x-text="recipe?.title"></h3>

                    <dl class="mt-4 grid grid-cols-2 gap-4 rounded-2xl bg-coffee-cream p-5">
                        <div>
                            <dt class="text-xs font-semibold text-coffee-mocha">{{ __('Bean Type') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-coffee-espresso">
                                <span x-text="recipe?.bean?.name"></span>
                                (<span x-text="recipe?.bean?.category"></span>)
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-coffee-mocha">{{ __('Method') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-coffee-espresso" x-text="recipe?.method?.name"></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-coffee-mocha">{{ __('Ratio') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-coffee-espresso" x-text="recipe?.ratio"></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-coffee-mocha">{{ __('Temp') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-coffee-espresso" x-text="recipe?.water_temp"></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-coffee-mocha">{{ __('Time') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-coffee-espresso">
                                <span x-text="recipe?.brew_time_min ? recipe.brew_time_min + {{ \Illuminate\Support\Js::from(' ' . __('min')) }} : '—'"></span>
                            </dd>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <dt class="text-xs font-semibold text-coffee-mocha">{{ __('Grind Size') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-coffee-espresso" x-text="recipe?.grind_size"></dd>
                        </div>
                    </dl>

                    <div class="mt-4">
                        <p class="text-sm font-semibold text-coffee-espresso">{{ __('Step-by-Step Instructions:') }}</p>
                        <ol class="mt-2 list-decimal list-inside space-y-1.5 text-sm text-coffee-mocha">
                            <template x-for="(step, i) in (recipe?.step_by_step ?? [])" :key="i">
                                <li><span x-text="step"></span></li>
                            </template>
                        </ol>
                    </div>

                    <a :href="recipe ? '/recipes/' + recipe.slug : '#'"
                       class="mt-5 inline-flex min-h-[44px] items-center justify-center rounded-xl border-2 border-coffee-espresso px-6 text-sm font-semibold text-coffee-espresso transition-colors duration-150 ease-out hover:bg-coffee-espresso hover:text-coffee-latte motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                        {{ __('View full recipe') }}
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection
