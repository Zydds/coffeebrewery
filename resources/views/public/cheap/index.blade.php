@extends('layouts.public')

@section('title', __('Cheap — Coffeebrewery'))

@section('meta_description', __('Great coffee on a budget — wallet-friendly recipes and cheap bean picks in one place.'))

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px]">
        <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Cheap') }}</h1>
    </div>

    {{-- Section divider --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[64px]">
        <hr class="border-t border-coffee-mocha/25">
    </div>

    {{-- Sachet coffee catalog --}}
    <section id="bean-catalog" data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[64px]">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Sachet & Instant Coffee') }}</h2>            <div class="flex flex-wrap gap-2" role="navigation" aria-label="{{ __('Filter sachet coffees by type') }}">
                <a href="{{ route('cheap.index', array_filter(['q' => request('q')])) }}"
                   class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('category') === null
                        ? 'bg-coffee-espresso text-coffee-latte'
                        : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                    {{ __('All') }}
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('cheap.index', array_filter(['q' => request('q'), 'category' => $category->slug])) }}"
                       class="min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] transition-colors {{ request('category') === $category->slug
                            ? 'bg-coffee-espresso text-coffee-latte'
                            : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <x-search-bar :action="route('cheap.index')" :placeholder="__('Search sachet and instant coffees…')" :value="request('q')" :preserves="['category' => request('category')]" />

        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($beans as $bean)
                <x-bean-card :bean="$bean" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-coffee-mocha/50 p-10 text-center">
                    <p class="text-sm text-coffee-mocha">{{ __('No sachet or instant coffees found.') }}</p>
                    @if (request('category') || request('q'))
                        <a href="{{ route('cheap.index') }}#bean-catalog" class="mt-3 inline-block text-sm font-semibold text-coffee-amber hover:underline">
                            {{ __('Clear search & filters') }}
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <x-page-arrows :paginator="$beans" />
    </section>

    {{-- Section divider --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px]">
        <hr class="border-t border-coffee-mocha/25">
    </div>

    {{-- Cheap brew methods --}}
    <section id="brew-methods" data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px]">
        <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Brew Methods') }}</h2>

        <x-search-bar :action="route('cheap.index')" name="method_q" :placeholder="__('Search brew methods…')" :value="request('method_q')" />

        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($methods as $method)
                <x-method-card :method="$method" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-coffee-mocha/50 p-10 text-center">
                    <p class="text-sm text-coffee-mocha">{{ __('No brew methods found.') }}</p>
                    @if (request('method_q'))
                        <a href="{{ route('cheap.index') }}#brew-methods" class="mt-3 inline-block text-sm font-semibold text-coffee-amber hover:underline">
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
    <section id="surprise" data-reveal class="mt-[88px] bg-coffee-roast py-[88px] text-coffee-cream" x-data="surpriseSection({ cheap: true })">
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

                <label class="mt-4 inline-flex items-center gap-2 text-sm text-coffee-cream cursor-pointer select-none">
                    <input type="checkbox" x-model="quick" @change="roll()"
                           class="h-4 w-4 rounded-sm border-coffee-latte/40 bg-transparent text-coffee-amber focus:ring-coffee-amber">
                    {{ __('Quick only (≤5 min)') }}
                </label>
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
                       class="mt-5 inline-flex min-h-[44px] items-center justify-center rounded-xl border-2 border-coffee-espresso px-6 text-sm font-semibold text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte transition-colors">
                        {{ __('View full recipe') }}
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection
