@extends('layouts.public')

@section('title', __('Custom — Coffeebrewery'))

@section('meta_description', __('Answer four quick questions about your ingredients, time, gear, and taste — we\'ll match you with a brew recipe that fits.'))

@section('content')
    <div class="relative isolate max-w-[1200px] mx-auto px-5 sm:px-8 pt-16 pb-10" x-data="brewFinder()">
        <x-sprig-side side="left" top="58" />
        <div class="max-w-2xl mx-auto text-center">
            <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Find Your Brew') }}</h1>
            <p class="mt-4 text-coffee-mocha">
                {{ __('Answer a few quick questions and we\'ll suggest the best recipe for you.') }}
            </p>
        </div>

        <div class="max-w-2xl mx-auto mt-10">
            {{-- Liquid progress bar --}}
            <div x-show="!finished && !loading && !error" x-cloak
                 class="flex items-center justify-between gap-4 text-sm text-coffee-mocha">
                <span>{{ __('Question') }} <span x-text="step + 1"></span> {{ __('of') }} <span x-text="total"></span></span>
                {{-- Segments: each is a clipping track. The inner fill div slides
                     in left-to-right via scaleX so transform is the only animating
                     property. Active = full fill; past = full fill; future = empty. --}}
                <div class="flex gap-1.5" aria-hidden="true">
                    <template x-for="(s, i) in steps" :key="s">
                        <div class="relative h-1.5 w-8 overflow-hidden rounded-full bg-coffee-mocha/20">
                            <div class="quiz-seg-fill absolute inset-0 origin-left rounded-full bg-coffee-amber"
                                 :class="i < step ? 'scale-x-100' : ''"
                                 :style="i === step ? 'animation: quiz-seg-in 400ms cubic-bezier(0.22,1,0.36,1) both' : ''">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Question card: fixed min-height so the footer never jumps
                 during the slide transition between steps. --}}
            <div class="relative isolate mt-4" x-show="!finished && !loading" x-cloak>
                <div x-ref="questions"
                     class="rounded-3xl border border-coffee-mocha/30 bg-coffee-cream p-6 sm:p-8"
                     style="min-height: 280px">

                    {{-- Q1: ingredients --}}
                    <div x-show="current === 'ingredients'">
                        <h2 class="font-display text-2xl font-bold text-coffee-espresso">{{ __('What coffee do you have?') }}</h2>
                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            @foreach ([
                                ['sachet',  __('Sachet'),        __('3-in-1 & instant packs')],
                                ['beans',   __('Whole beans'),   __('You have a grinder')],
                                ['ground',  __('Ground coffee'), __('Ready to brew')],
                            ] as [$val, $label, $sub])
                            <button type="button" @click="pick('ingredients', '{{ $val }}')"
                                    :aria-pressed="answers.ingredients === '{{ $val }}'"
                                    :class="{
                                        'border-coffee-amber ring-1 ring-coffee-amber': answers.ingredients === '{{ $val }}',
                                        'opacity-70': answers.ingredients && answers.ingredients !== '{{ $val }}',
                                        'border-coffee-mocha/30 hover:border-coffee-amber/60': !answers.ingredients || answers.ingredients !== '{{ $val }}'
                                    }"
                                    class="quiz-option relative rounded-xl border-2 bg-coffee-latte/40 p-4 text-left transition-[border-color,opacity,transform] duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-coffee-amber-ink">
                                {{-- Check mark: drawn when selected --}}
                                <span class="quiz-check absolute top-3 right-3 grid h-5 w-5 place-items-center rounded-full bg-coffee-amber text-coffee-espresso"
                                      :class="answers.ingredients === '{{ $val }}' ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                      style="transition: opacity 150ms cubic-bezier(0.22,1,0.36,1), transform 150ms cubic-bezier(0.22,1,0.36,1)">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9.5 18 20 6.5"/></svg>
                                </span>
                                <span class="block font-semibold text-coffee-espresso pr-6">{{ $label }}</span>
                                <span class="mt-1 block text-xs text-coffee-mocha">{{ $sub }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q2: time --}}
                    <div x-show="current === 'time'">
                        <h2 class="font-display text-2xl font-bold text-coffee-espresso">{{ __('How much time do you have?') }}</h2>
                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            @foreach ([
                                ['quick', __('Quick'),       __('5 minutes or less')],
                                ['ten',   __('About 10 min'),__('A short brew ritual')],
                                ['rush',  __('No rush'),     __('Enjoy the process')],
                            ] as [$val, $label, $sub])
                            <button type="button" @click="pick('time', '{{ $val }}')"
                                    :aria-pressed="answers.time === '{{ $val }}'"
                                    :class="{
                                        'border-coffee-amber ring-1 ring-coffee-amber': answers.time === '{{ $val }}',
                                        'opacity-70': answers.time && answers.time !== '{{ $val }}',
                                        'border-coffee-mocha/30 hover:border-coffee-amber/60': !answers.time || answers.time !== '{{ $val }}'
                                    }"
                                    class="quiz-option relative rounded-xl border-2 bg-coffee-latte/40 p-4 text-left transition-[border-color,opacity,transform] duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-coffee-amber-ink">
                                <span class="quiz-check absolute top-3 right-3 grid h-5 w-5 place-items-center rounded-full bg-coffee-amber text-coffee-espresso"
                                      :class="answers.time === '{{ $val }}' ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                      style="transition: opacity 150ms cubic-bezier(0.22,1,0.36,1), transform 150ms cubic-bezier(0.22,1,0.36,1)">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9.5 18 20 6.5"/></svg>
                                </span>
                                <span class="block font-semibold text-coffee-espresso pr-6">{{ $label }}</span>
                                <span class="mt-1 block text-xs text-coffee-mocha">{{ $sub }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q3: gear (skipped for sachet) --}}
                    <div x-show="current === 'gear'">
                        <h2 class="font-display text-2xl font-bold text-coffee-espresso">{{ __('What gear do you have?') }}</h2>

                        {{-- The one large illustration on the site: it swaps with
                             the chosen gear. x-if keeps exactly one SVG in the
                             DOM, so the per-part CSS runs once per choice. --}}
                        <div class="mt-5 flex justify-center" aria-hidden="true">
                            <template x-if="!answers.gear"><x-coffee-illustration type="bean" size="h-24 w-24 sm:h-28 sm:w-28" tone="amber-ink" /></template>
                            <template x-if="answers.gear === 'mug'"><x-coffee-illustration type="sachet" size="h-24 w-24 sm:h-28 sm:w-28" tone="amber-ink" /></template>
                            <template x-if="answers.gear === 'french-press'"><x-coffee-illustration type="french-press" size="h-24 w-24 sm:h-28 sm:w-28" tone="amber-ink" /></template>
                            <template x-if="answers.gear === 'v60'"><x-coffee-illustration type="v60" size="h-24 w-24 sm:h-28 sm:w-28" tone="amber-ink" /></template>
                            <template x-if="answers.gear === 'aeropress'"><x-coffee-illustration type="aeropress" size="h-24 w-24 sm:h-28 sm:w-28" tone="amber-ink" /></template>
                            <template x-if="answers.gear === 'espresso'"><x-coffee-illustration type="espresso" size="h-24 w-24 sm:h-28 sm:w-28" tone="amber-ink" /></template>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            @foreach ([
                                ['mug',          __('Just a mug'),        __('No brewing kit')],
                                ['french-press',  'French press',          __('Plunger pot')],
                                ['v60',          'Pour-over (V60)',        __('Dripper & filter')],
                                ['aeropress',    'Aeropress',              __('Press brewer')],
                                ['espresso',     __('Espresso machine'),   __('Pump machine')],
                            ] as [$val, $label, $sub])
                            <button type="button" @click="pick('gear', '{{ $val }}')"
                                    :aria-pressed="answers.gear === '{{ $val }}'"
                                    :class="{
                                        'border-coffee-amber ring-1 ring-coffee-amber': answers.gear === '{{ $val }}',
                                        'opacity-70': answers.gear && answers.gear !== '{{ $val }}',
                                        'border-coffee-mocha/30 hover:border-coffee-amber/60': !answers.gear || answers.gear !== '{{ $val }}'
                                    }"
                                    class="quiz-option relative rounded-xl border-2 bg-coffee-latte/40 p-4 text-left transition-[border-color,opacity,transform] duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-coffee-amber-ink">
                                <span class="quiz-check absolute top-3 right-3 grid h-5 w-5 place-items-center rounded-full bg-coffee-amber text-coffee-espresso"
                                      :class="answers.gear === '{{ $val }}' ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                      style="transition: opacity 150ms cubic-bezier(0.22,1,0.36,1), transform 150ms cubic-bezier(0.22,1,0.36,1)">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9.5 18 20 6.5"/></svg>
                                </span>
                                <span class="block font-semibold text-coffee-espresso pr-6">{{ $label }}</span>
                                <span class="mt-1 block text-xs text-coffee-mocha">{{ $sub }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q4: taste --}}
                    <div x-show="current === 'taste'">
                        <h2 class="font-display text-2xl font-bold text-coffee-espresso">{{ __('How do you like it?') }}</h2>
                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            @foreach ([
                                ['strong', __('Strong & bold'),   __('Full body, punchy')],
                                ['mild',   __('Smooth & mild'),   __('Easy drinking')],
                                ['fruity', __('Fruity & bright'), __('Acidic, floral notes')],
                            ] as [$val, $label, $sub])
                            <button type="button" @click="pick('taste', '{{ $val }}')"
                                    :aria-pressed="answers.taste === '{{ $val }}'"
                                    :class="{
                                        'border-coffee-amber ring-1 ring-coffee-amber': answers.taste === '{{ $val }}',
                                        'opacity-70': answers.taste && answers.taste !== '{{ $val }}',
                                        'border-coffee-mocha/30 hover:border-coffee-amber/60': !answers.taste || answers.taste !== '{{ $val }}'
                                    }"
                                    class="quiz-option relative rounded-xl border-2 bg-coffee-latte/40 p-4 text-left transition-[border-color,opacity,transform] duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-coffee-amber-ink">
                                <span class="quiz-check absolute top-3 right-3 grid h-5 w-5 place-items-center rounded-full bg-coffee-amber text-coffee-espresso"
                                      :class="answers.taste === '{{ $val }}' ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                      style="transition: opacity 150ms cubic-bezier(0.22,1,0.36,1), transform 150ms cubic-bezier(0.22,1,0.36,1)">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9.5 18 20 6.5"/></svg>
                                </span>
                                <span class="block font-semibold text-coffee-espresso pr-6">{{ $label }}</span>
                                <span class="mt-1 block text-xs text-coffee-mocha">{{ $sub }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q5: drink --}}
                    <div x-show="current === 'drink'">
                        <h2 class="font-display text-2xl font-bold text-coffee-espresso">{{ __('How do you drink it?') }}</h2>
                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach ([
                                ['black', __('Black'),      __('Straight, no milk')],
                                ['milk',  __('With milk'),  __('Creamy & comforting')],
                            ] as [$val, $label, $sub])
                            <button type="button" @click="pick('drink', '{{ $val }}')"
                                    :aria-pressed="answers.drink === '{{ $val }}'"
                                    :class="{
                                        'border-coffee-amber ring-1 ring-coffee-amber': answers.drink === '{{ $val }}',
                                        'opacity-70': answers.drink && answers.drink !== '{{ $val }}',
                                        'border-coffee-mocha/30 hover:border-coffee-amber/60': !answers.drink || answers.drink !== '{{ $val }}'
                                    }"
                                    class="quiz-option relative rounded-xl border-2 bg-coffee-latte/40 p-4 text-left transition-[border-color,opacity,transform] duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-coffee-amber-ink">
                                <span class="quiz-check absolute top-3 right-3 grid h-5 w-5 place-items-center rounded-full bg-coffee-amber text-coffee-espresso"
                                      :class="answers.drink === '{{ $val }}' ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                      style="transition: opacity 150ms cubic-bezier(0.22,1,0.36,1), transform 150ms cubic-bezier(0.22,1,0.36,1)">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9.5 18 20 6.5"/></svg>
                                </span>
                                <span class="block font-semibold text-coffee-espresso pr-6">{{ $label }}</span>
                                <span class="mt-1 block text-xs text-coffee-mocha">{{ $sub }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Nav buttons --}}
                    <div class="mt-8 flex items-center justify-between">
                        <button type="button" x-show="step > 0" @click="back()"
                                class="min-h-[44px] px-5 inline-flex items-center rounded-xl border-2 border-coffee-mocha/40 text-sm font-semibold text-coffee-mocha hover:border-coffee-espresso hover:text-coffee-espresso transition-colors duration-150 motion-safe:active:scale-[0.97] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-cream">
                            {{ __('Back') }}
                        </button>
                        <span x-show="step === 0"></span>

                        {{-- Continue: disabled = grey; enabled transitions to amber over 200ms --}}
                        <button type="button" @click="next()" :disabled="!answers[current]"
                                class="quiz-continue min-h-[44px] px-6 inline-flex items-center rounded-xl text-sm font-semibold transition-[background-color,color,opacity,transform] duration-200 ease-out motion-safe:active:scale-[0.97] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-cream"
                                :class="answers[current]
                                    ? 'bg-coffee-espresso text-coffee-latte hover:bg-coffee-amber hover:text-coffee-espresso'
                                    : 'bg-coffee-mocha/20 text-coffee-mocha cursor-not-allowed'">
                            <span x-show="step + 1 < total">{{ __('Continue') }}</span>
                            <span x-show="step + 1 >= total">{{ __('Show my recipe') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Loading --}}
            <div x-show="loading" x-cloak
                 class="mt-4 rounded-3xl border border-coffee-mocha/30 bg-coffee-cream p-10 text-center"
                 style="min-height: 280px">
                <p class="text-sm text-coffee-mocha">{{ __('Finding your brew…') }}</p>
            </div>

            {{-- Error --}}
            <div x-show="error && !loading" x-cloak
                 class="mt-4 rounded-3xl border border-red-300 bg-red-50 p-6 text-center">
                <p class="text-sm text-red-700">
                    <span x-text="error"></span>
                    <button type="button" @click="submit()" class="ml-1 font-semibold underline">{{ __('Try again') }}</button>
                </p>
            </div>

            {{-- Result: rises in with the same fade+lift as Surprise Me --}}
            <template x-if="finished && match && !loading">
                <div x-ref="results" aria-live="polite"
                     class="mt-4 rounded-3xl bg-coffee-roast p-6 sm:p-8 text-coffee-cream">
                    <p class="text-xs font-semibold uppercase tracking-wider text-coffee-latte/70">{{ __('We suggest') }}</p>

                    <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-coffee-latte" x-text="match.title"></h2>
                        <div class="flex shrink-0 items-center gap-1.5">
                            <span x-show="match.brew_time_min" class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-latte/15 text-coffee-latte"
                                  x-text="match.brew_time_min ? match.brew_time_min + {{ \Illuminate\Support\Js::from(' ' . __('min')) }} : ''"></span>
                            <span x-show="match.cheap" class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-amber text-coffee-espresso">{{ __('Cheap') }}</span>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-coffee-latte/80">
                        <span x-text="match.bean?.name"></span>
                        (<span x-text="match.bean?.category"></span>)
                        &middot;
                        <span x-text="match.method?.name"></span>
                    </p>

                    <dl class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div class="rounded-xl bg-coffee-latte/10 p-4 text-center">
                            <dt class="text-xs text-coffee-latte/70">{{ __('Ratio') }}</dt>
                            <dd class="mt-1 font-bold text-coffee-latte" x-text="match.ratio"></dd>
                        </div>
                        <div class="rounded-xl bg-coffee-latte/10 p-4 text-center">
                            <dt class="text-xs text-coffee-latte/70">{{ __('Temp') }}</dt>
                            <dd class="mt-1 font-bold text-coffee-latte" x-text="match.water_temp"></dd>
                        </div>
                        <div class="rounded-xl bg-coffee-latte/10 p-4 text-center">
                            <dt class="text-xs text-coffee-latte/70">{{ __('Grind') }}</dt>
                            <dd class="mt-1 font-bold text-coffee-latte" x-text="match.grind_size"></dd>
                        </div>
                        <div class="rounded-xl bg-coffee-latte/10 p-4 text-center">
                            <dt class="text-xs text-coffee-latte/70">{{ __('Time') }}</dt>
                            <dd class="mt-1 font-bold text-coffee-latte" x-text="match.brew_time_min ? match.brew_time_min + {{ \Illuminate\Support\Js::from(' ' . __('min')) }} : '—'"></dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a :href="'/recipes/' + match.slug"
                           class="min-h-[44px] px-6 inline-flex items-center rounded-xl bg-coffee-amber text-sm font-semibold text-coffee-espresso hover:bg-coffee-latte transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-roast">
                            {{ __('View full recipe') }}
                        </a>
                        <button type="button" x-show="matches.length > matchIndex + 1" @click="matchIndex++"
                                class="min-h-[44px] px-6 inline-flex items-center rounded-xl border-2 border-coffee-latte/40 text-sm font-semibold text-coffee-latte hover:bg-coffee-latte/10 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-roast">
                            {{ __('Show another match') }}
                        </button>
                        <button type="button" @click="restart()"
                                class="min-h-[44px] px-6 inline-flex items-center rounded-xl border-2 border-coffee-latte/40 text-sm font-semibold text-coffee-latte hover:bg-coffee-latte/10 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-roast">
                            {{ __('Start over') }}
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <x-wave-divider from-color="latte" to-color="espresso" />
@endsection
