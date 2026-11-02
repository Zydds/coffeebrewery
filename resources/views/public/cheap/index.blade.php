@extends('layouts.public')

@section('title', __('Cheap — Coffeebrewery'))

@section('meta_description', __('Great coffee on a budget — wallet-friendly recipes and cheap bean picks in one place.'))

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 section-top">
        <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Cheap') }}</h1>
    </div>

    {{-- Sachet coffee catalog --}}
    <section id="bean-catalog" data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 section-top">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Sachet & Instant Coffee') }}</h2>
            <div class="flex flex-wrap gap-2" role="navigation" aria-label="{{ __('Filter sachet coffees by type') }}">
                <x-chip :href="route('cheap.index', array_filter(['q' => request('q')]))" :active="request('category') === null">
                    {{ __('All') }}
                </x-chip>
                @foreach ($categories as $category)
                    <x-chip :href="route('cheap.index', array_filter(['q' => request('q'), 'category' => $category->slug]))"
                            :active="request('category') === $category->slug">
                        {{ $category->name }}
                    </x-chip>
                @endforeach
            </div>
        </div>

        <x-search-bar :action="route('cheap.index')" :placeholder="__('Search sachet and instant coffees…')" :value="request('q')" :preserves="['category' => request('category')]" />

        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($beans as $bean)
                <x-bean-card :bean="$bean" />
            @empty
                <x-empty-state :action="(request('category') || request('q')) ? __('Clear search & filters') : null"
                               :href="route('cheap.index') . '#bean-catalog'">
                    {{ __('No sachet or instant coffees found.') }}
                </x-empty-state>
            @endforelse
        </div>

        <x-page-arrows :paginator="$beans" />
    </section>

    {{-- Cheap brew methods — cream band, matching the home page --}}
    <div class="bg-coffee-cream">
        <section id="brew-methods" data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 section-top pb-22">
            <h2 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Brew Methods') }}</h2>

            <x-search-bar :action="route('cheap.index')" name="method_q" :placeholder="__('Search brew methods…')" :value="request('method_q')" />

            <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($methods as $method)
                    <x-method-card :method="$method" />
                @empty
                    <x-empty-state :action="request('method_q') ? __('Clear search') : null"
                                   :href="route('cheap.index') . '#brew-methods'">
                        {{ __('No brew methods found.') }}
                    </x-empty-state>
                @endforelse
            </div>

            <x-page-arrows :paginator="$methods" />
        </section>
    </div>

    {{-- Surprise me --}}
    <section id="surprise" data-reveal-stagger class="bg-coffee-roast py-[88px] text-coffee-cream" x-data="surpriseSection({ cheap: true })">
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

                <label class="mt-4 inline-flex items-center gap-2 text-sm text-coffee-cream cursor-pointer select-none">
                    <input type="checkbox" x-model="quick" @change="roll()"
                           class="h-4 w-4 rounded-sm border-coffee-latte/40 bg-transparent text-coffee-amber focus:ring-coffee-amber">
                    {{ __('Quick only (≤5 min)') }}
                </label>
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
