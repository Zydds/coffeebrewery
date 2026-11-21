{{-- The dark "Can't decide what to brew?" band, shared by home and Cheap.
     cheap-only = this page only rolls cheap recipes, so the Cheap toggle
     would be a no-op and is omitted. The roll/card animation lives in
     app.js (surpriseSection); the checkbox draw in app.css. --}}
@props(['cheapOnly' => false])

<section id="surprise" class="relative isolate bg-coffee-roast py-[88px] text-coffee-cream"
         x-data="surpriseSection({{ $cheapOnly ? '{ cheap: true }' : '{}' }})">
    <div data-reveal-stagger class="max-w-[1200px] mx-auto px-5 sm:px-8 grid gap-10 lg:grid-cols-[.85fr_1.15fr] items-center">
        <div>
            <h2 class="font-display font-bold text-section text-coffee-latte">{{ __('Can\'t decide what to brew?') }}</h2>
            <p class="mt-4 max-w-[420px]">{{ __('Roll a random recipe. Every roll gives you a different one.') }}</p>
            <button type="button"
                    @click="rollNext()"
                    :disabled="loading"
                    class="mt-8 min-h-[52px] px-7 inline-flex items-center gap-2.5 rounded-xl bg-coffee-amber font-semibold text-coffee-espresso transition-colors duration-150 ease-out hover:bg-coffee-latte motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte disabled:opacity-60">
                <x-dice-icon :size="22" />
                <span x-text="rolled ? {{ \Illuminate\Support\Js::from(__('Roll another')) }} : {{ \Illuminate\Support\Js::from(__('Surprise me')) }}"></span>
            </button>

            <div class="mt-3 flex flex-col items-start">
                @unless ($cheapOnly)
                    <label class="inline-flex min-h-[44px] items-center gap-2.5 text-sm text-coffee-cream cursor-pointer select-none">
                        <input type="checkbox" x-model="cheap" @change="roll()" class="check-input peer sr-only">
                        <span class="check-box grid h-5 w-5 shrink-0 place-items-center rounded-md border-2 border-coffee-latte/50 peer-checked:border-coffee-amber peer-checked:bg-coffee-amber hover-fine:border-coffee-latte peer-focus-visible:ring-2 peer-focus-visible:ring-coffee-latte peer-focus-visible:ring-offset-2 peer-focus-visible:ring-offset-coffee-roast">
                            <svg class="check-mark h-3 w-3 text-coffee-espresso" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 12.5 9.5 18 20 6.5"/>
                            </svg>
                        </span>
                        {{ __('Cheap only') }}
                    </label>
                @endunless

                <label class="inline-flex min-h-[44px] items-center gap-2.5 text-sm text-coffee-cream cursor-pointer select-none">
                    <input type="checkbox" x-model="quick" @change="roll()" class="check-input peer sr-only">
                    <span class="check-box grid h-5 w-5 shrink-0 place-items-center rounded-md border-2 border-coffee-latte/50 peer-checked:border-coffee-amber peer-checked:bg-coffee-amber hover-fine:border-coffee-latte peer-focus-visible:ring-2 peer-focus-visible:ring-coffee-latte peer-focus-visible:ring-offset-2 peer-focus-visible:ring-offset-coffee-roast">
                        <svg class="check-mark h-3 w-3 text-coffee-espresso" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 12.5 9.5 18 20 6.5"/>
                        </svg>
                    </span>
                    {{ __('Quick only (≤5 min)') }}
                </label>
            </div>
        </div>

        <article class="flex flex-col gap-5 rounded-3xl bg-coffee-latte p-6 sm:p-8 text-coffee-espresso" aria-live="polite">
            <p x-show="loading" x-cloak class="text-sm text-coffee-mocha">{{ __('Brewing a suggestion…') }}</p>

            <p x-show="error" x-cloak class="text-sm text-coffee-espresso">
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
                   class="mt-5 inline-flex min-h-[44px] items-center justify-center rounded-xl border-2 border-coffee-espresso px-6 text-sm font-semibold text-coffee-espresso transition-[color,background-color,transform] duration-150 ease-out hover:bg-coffee-espresso hover:text-coffee-latte motion-safe:active:scale-[0.97] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                    {{ __('View full recipe') }}
                </a>
            </div>
        </article>
    </div>
</section>
