@extends('layouts.public')

@section('title', $recipe->title . ' — Coffeebrewery')

@section('meta_description', $seo->description())

@section('og_type', 'article')

{{-- Recipes carry no image of their own — the paired bean's photo is the
     closest representation, and only pages that have one get og:image. --}}
@section('og_image', $seo->image() ?? '')

@section('content')
    <script type="application/ld+json">{!! $seo->jsonLd() !!}</script>

    @php($steps = collect($recipe->step_by_step ?? [])->map(fn ($step) => __($step))->values())

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
         x-data="brewMode(@js($steps), {{ (int) ($recipe->brew_time_min ?? 0) }})">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('recipes.index') }}"
               class="min-h-[44px] px-6 inline-flex items-center rounded-xl bg-coffee-espresso text-sm font-semibold text-coffee-latte transition-colors duration-150 ease-out hover:bg-coffee-amber hover:text-coffee-espresso motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                {{ __('Explore Recipes') }}
            </a>
            <a href="{{ route('home') }}"
               class="min-h-[44px] px-6 inline-flex items-center rounded-xl border-2 border-coffee-espresso text-sm font-semibold text-coffee-espresso transition-colors duration-150 ease-out hover:bg-coffee-espresso hover:text-coffee-latte motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                {{ __('Back to Home') }}
            </a>
        </div>

        <div data-reveal class="mt-6 rounded-2xl border border-coffee-mocha/30 bg-coffee-cream p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <h1 class="font-display text-[clamp(2rem,4vw,3rem)] font-extrabold leading-[1.1] tracking-tight text-coffee-espresso">{{ $recipe->title }}</h1>
                <div class="flex shrink-0 items-center gap-1.5">
                    @if ($recipe->brew_time_min)
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-mocha/15 text-coffee-mocha">
                            {{ $recipe->brew_time_min }} {{ __('min') }}
                        </span>
                    @endif
                    <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $recipe->cheap
                        ? 'bg-coffee-amber text-coffee-espresso'
                        : 'bg-coffee-mocha/15 text-coffee-mocha' }}">
                        {{ $recipe->cheap ? __('Cheap') : __('Non-cheap') }}
                    </span>
                    @if ($recipe->is_featured)
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-espresso text-coffee-latte">{{ __('Featured') }}</span>
                    @endif
                </div>
            </div>

            {{-- Pairing --}}
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <a href="{{ $recipe->coffeeBean ? route('beans.show', $recipe->coffeeBean->slug) : route('home').'#bean-catalog' }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-coffee-mocha/40 text-coffee-espresso transition-colors duration-150 ease-out hover:border-coffee-amber focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                    <span class="text-coffee-mocha text-xs">{{ __('Bean:') }}</span>
                    <span class="font-medium">{{ $recipe->coffeeBean->name ?? __('Unknown') }}</span>
                    @if ($recipe->coffeeBean?->category)
                        <span class="text-xs text-coffee-mocha">({{ $recipe->coffeeBean->category->name }})</span>
                    @endif
                </a>
                <a href="{{ $recipe->brewMethod ? route('methods.show', $recipe->brewMethod->slug) : route('home').'#brew-methods' }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-coffee-mocha/40 text-coffee-espresso transition-colors duration-150 ease-out hover:border-coffee-amber focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                    <span class="text-coffee-mocha text-xs">{{ __('Method:') }}</span>
                    <span class="font-medium">{{ $recipe->brewMethod->name ?? __('Unknown') }}</span>
                </a>
            </div>

            {{-- Specs --}}
            <dl class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @if ($recipe->brew_time_min)
                    <div class="rounded-xl bg-white border border-coffee-mocha/20 p-4 text-center">
                        <dt class="text-xs uppercase tracking-wider text-coffee-mocha">{{ __('Total Time') }}</dt>
                        <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->brew_time_min }} {{ __('min') }}</dd>
                    </div>
                @endif
                <div class="rounded-sm bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">{{ __('Ratio') }}</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->ratio }}</dd>
                </div>
                <div class="rounded-sm bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">{{ __('Water Temp') }}</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->water_temp_c }}°C</dd>
                </div>
                <div class="rounded-sm bg-white border border-coffee-mocha/20 p-4 text-center">
                    <dt class="text-xs uppercase tracking-wider text-coffee-mocha">{{ __('Grind Size') }}</dt>
                    <dd class="mt-1 font-bold text-coffee-espresso">{{ $recipe->grind_size }}</dd>
                </div>
            </dl>

            @if ($scale = $seo->scale())
                <div class="mt-4 rounded-xl bg-white border border-coffee-mocha/20 p-4 flex flex-wrap items-center justify-between gap-4"
                     x-data="servings({{ $scale['coffee_g'] }}, {{ $scale['water_ml'] }})">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-coffee-mocha">
                            {{ __('Brew amounts') }}<span x-show="n > 1" x-text="' — ' + n + {{ \Illuminate\Support\Js::from(__(' servings')) }}"></span>
                        </p>
                        <p class="mt-1 font-bold text-coffee-espresso">
                            <span x-text="coffee * n"></span>{{ __('g coffee') }} &rarr; <span x-text="water * n"></span>{{ __('ml water') }}
                        </p>
                    </div>
                    <div class="flex gap-1.5" role="group" aria-label="{{ __('Number of servings') }}">
                        <template x-for="i in [1, 2, 3]" :key="i">
                            <button type="button" @click="n = i" :aria-pressed="n === i"
                                    :class="n === i ? 'bg-coffee-espresso text-coffee-latte' : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-mocha/20'"
                                    class="h-10 w-10 rounded-xl text-sm font-semibold transition-colors"
                                    x-text="i + '×'"></button>
                        </template>
                    </div>
                </div>
            @endif

            {{-- Steps --}}
            <div class="mt-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-coffee-espresso">{{ __('Step-by-Step Instructions') }}</h2>
                    @if ($steps->isNotEmpty())
                        <button type="button" @click="openMode()"
                                class="inline-flex min-h-[44px] items-center gap-2 rounded-xl bg-coffee-espresso px-5 text-sm font-semibold text-coffee-latte transition-colors duration-150 ease-out hover:bg-coffee-amber hover:text-coffee-espresso motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                            {{ __('Brew mode') }}
                        </button>
                    @endif
                </div>
                <ol class="mt-3 list-decimal list-inside space-y-2 text-sm text-coffee-mocha">
                    @foreach ($steps as $step)
                        <li class="pl-1">{{ $step }}</li>
                    @endforeach
                </ol>
            </div>
        </div>

        {{-- Full-screen brew mode. Native <dialog> gives the focus trap,
             Escape handling, and backdrop without extra JS. --}}
        <dialog x-ref="dialog" @close="cleanup()" aria-label="{{ __('Brew mode') }}"
                class="brew-dialog m-0 h-dvh max-h-none w-screen max-w-none border-0 bg-coffee-espresso p-0 text-coffee-cream">
            <div class="mx-auto flex min-h-dvh w-full max-w-2xl flex-col justify-between gap-8 px-5 py-6 sm:px-8">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">
                        {{ $recipe->title }}
                    </p>
                    <button type="button" x-ref="closeBtn" @click="closeMode()"
                            class="grid h-11 w-11 place-items-center rounded-full text-coffee-cream transition-colors hover:bg-coffee-cream/15 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-espresso"
                            aria-label="{{ __('Close') }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                            <use href="#cb-close" />
                        </svg>
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-coffee-cream/70">{{ __('Time left') }}</p>
                    <p class="mt-2 font-display text-[clamp(3.5rem,14vw,6rem)] font-extrabold leading-none tabular-nums text-coffee-amber" x-text="clock">00:00</p>

                    @if ((int) ($recipe->brew_time_min ?? 0) > 0)
                        <button type="button" @click="toggleTimer()"
                                class="mt-5 inline-flex min-h-[44px] items-center rounded-xl bg-coffee-amber px-6 text-sm font-semibold text-coffee-espresso transition-colors hover:bg-coffee-latte focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-espresso">
                            <span x-show="!ticker">{{ __('Start') }}</span>
                            <span x-show="ticker" x-cloak>{{ __('Pause') }}</span>
                        </button>
                    @endif
                </div>

                <div class="flex-1">
                    <p class="text-sm font-semibold text-coffee-cream/70">
                        {{ __('Step') }} <span x-text="step + 1"></span> {{ __('of') }} <span x-text="steps.length"></span>
                    </p>
                    <p class="mt-3 font-display text-[clamp(1.5rem,4vw,2rem)] font-bold leading-snug text-coffee-latte"
                       x-text="steps[step]"></p>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <button type="button" @click="step = Math.max(0, step - 1)" :disabled="step === 0"
                            class="inline-flex min-h-[48px] items-center rounded-xl border-2 border-coffee-cream/40 px-5 text-sm font-semibold text-coffee-cream transition-colors hover:border-coffee-latte disabled:opacity-40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte">
                        {{ __('Back') }}
                    </button>

                    <button type="button" x-show="step + 1 < steps.length" @click="step++"
                            class="inline-flex min-h-[48px] items-center rounded-xl bg-coffee-cream px-6 text-sm font-semibold text-coffee-espresso transition-colors hover:bg-coffee-amber focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte">
                        {{ __('Next') }}
                    </button>

                    <button type="button" x-show="step + 1 >= steps.length" x-cloak @click="closeMode()"
                            class="inline-flex min-h-[48px] items-center rounded-xl bg-coffee-amber px-6 text-sm font-semibold text-coffee-espresso transition-colors hover:bg-coffee-latte focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte">
                        {{ __('Done') }}
                    </button>
                </div>
            </div>
        </dialog>
    </div>
@endsection
