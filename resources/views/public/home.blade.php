@extends('layouts.public')

@section('title', __('Coffeebrewery — Find Your Bean, Nail Your Brew'))

@section('content')
    {{-- Hero --}}
    <section class="bg-coffee-cream">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-16 lg:py-[72px] grid gap-10 lg:grid-cols-[1.1fr_.9fr] items-center">
            <div>
                <h1 class="font-display font-extrabold leading-[1.1] tracking-tight text-[clamp(2.6rem,6vw,4.5rem)]">
                    {{ __('Find your bean.') }}
                    <mark class="rounded-[10px] bg-coffee-amber px-[0.16em] text-coffee-espresso [box-decoration-break:clone] [-webkit-box-decoration-break:clone]">{{ __('Nail your brew.') }}</mark>
                </h1>
                <p class="mt-6 max-w-[480px] text-lg text-coffee-mocha">
                    {{ __('Browse beans by origin, learn how each brew method works, and get a recipe when you cannot decide.') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#surprise"
                       class="min-h-[52px] px-7 inline-flex items-center gap-2.5 rounded-xl bg-coffee-espresso font-semibold text-coffee-latte hover:bg-coffee-amber hover:text-coffee-espresso transition-colors">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3.5" y="3.5" width="17" height="17" rx="3"/>
                            <circle cx="8.5" cy="8.5" r="1" fill="currentColor"/>
                            <circle cx="15.5" cy="15.5" r="1" fill="currentColor"/>
                            <circle cx="12" cy="12" r="1" fill="currentColor"/>
                        </svg>
                        {{ __('Surprise me') }}
                    </a>
                    <a href="{{ route('recipes.index', ['quick' => 1]) }}"
                       class="min-h-[52px] px-7 inline-flex items-center rounded-xl border-2 border-coffee-espresso font-semibold text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte transition-colors">
                        {{ __('Quick recipes ≤5 min') }}
                    </a>
                </div>
            </div>

            <div class="w-full max-w-[460px] justify-self-center">
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

    {{-- Section divider --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8">
        <hr class="border-t border-coffee-mocha/25">
    </div>

    {{-- Bean catalog --}}
    <section id="bean-catalog" data-reveal class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px]">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Bean Catalog') }}</h2>
            <div class="flex flex-wrap gap-2" role="navigation" aria-label="{{ __('Filter beans by type') }}">
                <a href="{{ route('home', array_filter(['q' => request('q')])) }}"
                   class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('category') === null
                        ? 'bg-coffee-espresso text-coffee-latte'
                        : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                    {{ __('All') }}
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('home', array_filter(['q' => request('q'), 'category' => $category->slug])) }}"
                       class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('category') === $category->slug
                            ? 'bg-coffee-espresso text-coffee-latte'
                            : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <x-search-bar :action="route('home')" :placeholder="__('Search beans…')" :value="request('q')" :preserves="['category' => request('category')]" />

        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($featuredBeans as $bean)
                <x-bean-card :bean="$bean" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-coffee-mocha/50 p-10 text-center">
                    <p class="text-sm text-coffee-mocha">{{ __('No beans found.') }}</p>
                    @if (request('category') || request('q'))
                        <a href="{{ route('home') }}#bean-catalog" class="mt-3 inline-block text-sm font-semibold text-coffee-amber hover:underline">
                            {{ __('Clear search & filters') }}
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <x-page-arrows :paginator="$featuredBeans" />
    </section>

    {{-- Section divider --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px]">
        <hr class="border-t border-coffee-mocha/25">
    </div>

    {{-- Brew methods --}}
    <section id="brew-methods" data-reveal class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px]">
        <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Brew Methods') }}</h2>

        <x-search-bar :action="route('home')" name="method_q" :placeholder="__('Search brew methods…')" :value="request('method_q')" />

        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($methods as $method)
                <x-method-card :method="$method" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-coffee-mocha/50 p-10 text-center">
                    <p class="text-sm text-coffee-mocha">{{ __('No brew methods found.') }}</p>
                    @if (request('method_q'))
                        <a href="{{ route('home') }}#brew-methods" class="mt-3 inline-block text-sm font-semibold text-coffee-amber hover:underline">
                            {{ __('Clear search') }}
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <x-page-arrows :paginator="$methods" />
    </section>

    {{-- Section divider --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px]">
        <hr class="border-t border-coffee-mocha/25">
    </div>

    {{-- Surprise me --}}
    <section id="surprise" data-reveal class="mt-[88px] bg-coffee-roast py-[88px] text-coffee-cream" x-data="surpriseSection()">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 grid gap-10 lg:grid-cols-[.85fr_1.15fr] items-center">
            <div>
                <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)] text-coffee-latte">{{ __('Can\'t decide what to brew?') }}</h2>
                <p class="mt-4 max-w-[420px]">{{ __('Roll a random recipe. Every roll gives you a different one.') }}</p>
                <button type="button"
                        @click="rollNext()"
                        :disabled="loading"
                        class="mt-8 min-h-[52px] px-7 inline-flex items-center gap-2.5 rounded-xl bg-coffee-amber font-semibold text-coffee-espresso hover:bg-coffee-latte transition-colors disabled:opacity-60">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3.5" y="3.5" width="17" height="17" rx="3"/>
                        <circle cx="8.5" cy="8.5" r="1" fill="currentColor"/>
                        <circle cx="15.5" cy="15.5" r="1" fill="currentColor"/>
                        <circle cx="12" cy="12" r="1" fill="currentColor"/>
                    </svg>
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

            <article class="flex flex-col gap-5 rounded-[20px] bg-coffee-latte p-6 sm:p-8 text-coffee-espresso" aria-live="polite">
                <p x-show="loading" x-cloak class="text-sm text-coffee-mocha">{{ __('Brewing a suggestion…') }}</p>

                <p x-show="error" x-cloak class="text-sm text-red-700">
                    <span x-text="error"></span>
                    <button type="button" @click="roll(recipe?.id ?? null)" class="font-semibold underline">{{ __('Try again') }}</button>
                </p>

                <p x-show="empty && !loading && !error" x-cloak class="text-sm text-coffee-mocha">
                    {{ __('No recipes yet. Check back soon.') }}
                </p>

                <div x-show="recipe && !loading && !error" x-cloak>
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
                       class="mt-5 inline-flex min-h-[44px] items-center justify-center rounded-xl border-2 border-coffee-espresso px-6 text-sm font-semibold text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte transition-colors">
                        {{ __('View full recipe') }}
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection
