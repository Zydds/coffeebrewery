@extends('layouts.public')

@section('title', 'Custom — Coffeebrewery')

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-[88px]" x-data="brewFinder()">
        <div class="max-w-2xl mx-auto text-center">
            <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">Find Your Brew</h1>
            <p class="mt-4 text-coffee-mocha">
                Answer a few quick questions and we'll suggest the best recipe for you.
            </p>
        </div>

        <div class="max-w-2xl mx-auto mt-10">
            {{-- Progress --}}
            <div x-show="!finished && !loading && !error" x-cloak class="flex items-center justify-between gap-4 text-sm text-coffee-mocha">
                <span>Question <span x-text="step + 1"></span> of <span x-text="total"></span></span>
                <div class="flex gap-1.5" aria-hidden="true">
                    <template x-for="i in total" :key="i">
                        <span class="h-1.5 w-8 rounded-full transition-colors" :class="i <= step + 1 ? 'bg-coffee-amber' : 'bg-coffee-mocha/20'"></span>
                    </template>
                </div>
            </div>

            {{-- Question card --}}
            <div x-show="!finished" x-cloak class="mt-4 rounded-[20px] border border-coffee-mocha/30 bg-coffee-cream p-6 sm:p-8">
                {{-- Q1: ingredients --}}
                <div x-show="current === 'ingredients'">
                    <h2 class="font-display text-2xl font-bold text-coffee-espresso">What coffee do you have?</h2>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <button type="button" @click="pick('ingredients', 'sachet')"
                                :class="answers.ingredients === 'sachet' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Sachet</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">3-in-1 &amp; instant packs</span>
                        </button>
                        <button type="button" @click="pick('ingredients', 'beans')"
                                :class="answers.ingredients === 'beans' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Whole beans</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">You have a grinder</span>
                        </button>
                        <button type="button" @click="pick('ingredients', 'ground')"
                                :class="answers.ingredients === 'ground' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Ground coffee</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Ready to brew</span>
                        </button>
                    </div>
                </div>

                {{-- Q2: time --}}
                <div x-show="current === 'time'">
                    <h2 class="font-display text-2xl font-bold text-coffee-espresso">How much time do you have?</h2>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <button type="button" @click="pick('time', 'quick')"
                                :class="answers.time === 'quick' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Quick</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">5 minutes or less</span>
                        </button>
                        <button type="button" @click="pick('time', 'ten')"
                                :class="answers.time === 'ten' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">About 10 min</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">A short brew ritual</span>
                        </button>
                        <button type="button" @click="pick('time', 'rush')"
                                :class="answers.time === 'rush' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">No rush</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Enjoy the process</span>
                        </button>
                    </div>
                </div>

                {{-- Q3: gear (skipped for sachet) --}}
                <div x-show="current === 'gear'">
                    <h2 class="font-display text-2xl font-bold text-coffee-espresso">What gear do you have?</h2>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <button type="button" @click="pick('gear', 'mug')"
                                :class="answers.gear === 'mug' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Just a mug</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">No brewing kit</span>
                        </button>
                        <button type="button" @click="pick('gear', 'french-press')"
                                :class="answers.gear === 'french-press' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">French press</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Plunger pot</span>
                        </button>
                        <button type="button" @click="pick('gear', 'v60')"
                                :class="answers.gear === 'v60' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Pour-over (V60)</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Dripper &amp; filter</span>
                        </button>
                        <button type="button" @click="pick('gear', 'aeropress')"
                                :class="answers.gear === 'aeropress' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Aeropress</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Press brewer</span>
                        </button>
                        <button type="button" @click="pick('gear', 'espresso')"
                                :class="answers.gear === 'espresso' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Espresso machine</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Pump machine</span>
                        </button>
                    </div>
                </div>

                {{-- Q4: taste --}}
                <div x-show="current === 'taste'">
                    <h2 class="font-display text-2xl font-bold text-coffee-espresso">How do you like it?</h2>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <button type="button" @click="pick('taste', 'strong')"
                                :class="answers.taste === 'strong' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Strong &amp; bold</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Full body, punchy</span>
                        </button>
                        <button type="button" @click="pick('taste', 'mild')"
                                :class="answers.taste === 'mild' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Smooth &amp; mild</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Easy drinking</span>
                        </button>
                        <button type="button" @click="pick('taste', 'fruity')"
                                :class="answers.taste === 'fruity' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Fruity &amp; bright</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Acidic, floral notes</span>
                        </button>
                    </div>
                </div>

                {{-- Q5: drink --}}
                <div x-show="current === 'drink'">
                    <h2 class="font-display text-2xl font-bold text-coffee-espresso">How do you drink it?</h2>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <button type="button" @click="pick('drink', 'black')"
                                :class="answers.drink === 'black' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">Black</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Straight, no milk</span>
                        </button>
                        <button type="button" @click="pick('drink', 'milk')"
                                :class="answers.drink === 'milk' ? 'border-coffee-amber ring-1 ring-coffee-amber' : 'border-coffee-mocha/30 hover:border-coffee-amber'"
                                class="rounded-xl border-2 bg-white p-4 text-left transition-colors">
                            <span class="block font-semibold text-coffee-espresso">With milk</span>
                            <span class="mt-1 block text-xs text-coffee-mocha">Creamy &amp; comforting</span>
                        </button>
                    </div>
                </div>

                {{-- Nav buttons --}}
                <div class="mt-8 flex items-center justify-between">
                    <button type="button" x-show="step > 0" @click="back()"
                            class="min-h-[44px] px-5 inline-flex items-center rounded-xl border-2 border-coffee-mocha/40 text-sm font-semibold text-coffee-mocha hover:border-coffee-espresso hover:text-coffee-espresso transition-colors">
                        Back
                    </button>
                    <span x-show="step === 0"></span>
                    <button type="button" @click="next()" :disabled="!answers[current]"
                            class="min-h-[44px] px-6 inline-flex items-center rounded-xl bg-coffee-espresso text-sm font-semibold text-coffee-latte hover:bg-coffee-amber hover:text-coffee-espresso transition-colors disabled:opacity-40">
                        <span x-show="step + 1 < total">Continue</span>
                        <span x-show="step + 1 >= total">Show my recipe</span>
                    </button>
                </div>
            </div>

            {{-- Loading --}}
            <div x-show="loading" x-cloak class="mt-4 rounded-[20px] border border-coffee-mocha/30 bg-coffee-cream p-10 text-center">
                <p class="text-sm text-coffee-mocha">Finding your brew&hellip;</p>
            </div>

            {{-- Error --}}
            <div x-show="error && !loading" x-cloak class="mt-4 rounded-[20px] border border-red-300 bg-red-50 p-6 text-center">
                <p class="text-sm text-red-700">
                    <span x-text="error"></span>
                    <button type="button" @click="submit()" class="ml-1 font-semibold underline">Try again</button>
                </p>
            </div>

            {{-- Result --}}
            <div x-show="finished && match && !loading" x-cloak aria-live="polite"
                 class="mt-4 rounded-[20px] bg-coffee-roast p-6 sm:p-8 text-coffee-cream">
                <p class="text-xs font-semibold uppercase tracking-wider text-coffee-latte/70">We suggest</p>

                <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
                    <h2 class="font-display text-2xl sm:text-3xl font-bold text-coffee-latte" x-text="match.title"></h2>
                    <div class="flex shrink-0 items-center gap-1.5">
                        <span x-show="match.brew_time_min" class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-latte/15 text-coffee-latte"
                              x-text="match.brew_time_min ? match.brew_time_min + ' min' : ''"></span>
                        <span x-show="match.cheap" class="text-xs font-semibold px-3 py-1 rounded-full bg-coffee-amber text-coffee-espresso">Cheap</span>
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
                        <dt class="text-xs text-coffee-latte/70">Ratio</dt>
                        <dd class="mt-1 font-bold text-coffee-latte" x-text="match.ratio"></dd>
                    </div>
                    <div class="rounded-xl bg-coffee-latte/10 p-4 text-center">
                        <dt class="text-xs text-coffee-latte/70">Temp</dt>
                        <dd class="mt-1 font-bold text-coffee-latte" x-text="match.water_temp"></dd>
                    </div>
                    <div class="rounded-xl bg-coffee-latte/10 p-4 text-center">
                        <dt class="text-xs text-coffee-latte/70">Grind</dt>
                        <dd class="mt-1 font-bold text-coffee-latte" x-text="match.grind_size"></dd>
                    </div>
                    <div class="rounded-xl bg-coffee-latte/10 p-4 text-center">
                        <dt class="text-xs text-coffee-latte/70">Time</dt>
                        <dd class="mt-1 font-bold text-coffee-latte" x-text="match.brew_time_min ? match.brew_time_min + ' min' : '—'"></dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a :href="'/recipes/' + match.slug"
                       class="min-h-[44px] px-6 inline-flex items-center rounded-xl bg-coffee-amber text-sm font-semibold text-coffee-espresso hover:bg-coffee-latte transition-colors">
                        View full recipe
                    </a>
                    <button type="button" x-show="matches.length > matchIndex + 1" @click="matchIndex++"
                            class="min-h-[44px] px-6 inline-flex items-center rounded-xl border-2 border-coffee-latte/40 text-sm font-semibold text-coffee-latte hover:bg-coffee-latte/10 transition-colors">
                        Show another match
                    </button>
                    <button type="button" @click="restart()"
                            class="min-h-[44px] px-6 inline-flex items-center rounded-xl border-2 border-coffee-latte/40 text-sm font-semibold text-coffee-latte hover:bg-coffee-latte/10 transition-colors">
                        Start over
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
