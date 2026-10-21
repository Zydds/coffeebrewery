<div x-data="surpriseModal()"
     @open-surprise.window="show()"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="surprise-title">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-coffee-espresso/70" @click="open = false"></div>

    <div class="relative w-full max-w-xl max-h-[90vh] overflow-y-auto bg-coffee-latte rounded-lg shadow-xl border border-coffee-mocha/40"
         @click.stop>
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 bg-coffee-espresso text-coffee-latte rounded-t-lg">
            <h2 id="surprise-title" class="font-semibold tracking-wide text-sm sm:text-base">
                &#127922; RANDOM RECIPE GENERATOR
            </h2>
            <button type="button" @click="open = false" class="p-1 rounded hover:bg-white/10 transition-colors" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-5 py-5">
            {{-- Loading state --}}
            <div x-show="loading" class="py-10 text-center text-coffee-mocha" x-cloak>
                <svg class="w-8 h-8 mx-auto animate-spin text-coffee-amber" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <p class="mt-3 text-sm">Brewing a suggestion&hellip;</p>
            </div>

            {{-- Error state --}}
            <div x-show="!loading && error !== ''" class="py-8 text-center" x-cloak>
                <p class="text-sm text-coffee-espresso font-medium"><span x-text="error"></span></p>
                <button type="button" @click="roll()"
                        class="mt-4 px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                    Try Again
                </button>
            </div>

            {{-- Empty state --}}
            <div x-show="!loading && empty && !recipe" class="py-8 text-center" x-cloak>
                <p class="text-sm text-coffee-espresso font-medium">No recipes in the database yet.</p>
                <p class="mt-1 text-xs text-coffee-mocha">Add a recipe in the admin panel first.</p>
            </div>

            {{-- Recipe content --}}
            <div x-show="!loading && recipe !== null" x-cloak>
                <p class="text-xs uppercase tracking-wider text-coffee-mocha">✨ Recommended Pairing:</p>
                <h3 class="mt-1 text-xl font-bold text-coffee-espresso" x-text="recipe?.title"></h3>

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-coffee-mocha">Bean Type:</dt>
                        <dd class="font-medium text-coffee-espresso">
                            <span x-text="recipe?.bean?.name"></span>
                            (<span x-text="recipe?.bean?.category"></span>)
                        </dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-coffee-mocha">Method:</dt>
                        <dd class="font-medium text-coffee-espresso" x-text="recipe?.method?.name"></dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-coffee-mocha">Ratio:</dt>
                        <dd class="font-medium text-coffee-espresso" x-text="recipe?.ratio"></dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-coffee-mocha">Temp:</dt>
                        <dd class="font-medium text-coffee-espresso" x-text="recipe?.water_temp"></dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-coffee-mocha">Grind Size:</dt>
                        <dd class="font-medium text-coffee-espresso" x-text="recipe?.grind_size"></dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <p class="text-sm font-semibold text-coffee-espresso">Step-by-Step Instructions:</p>
                    <ol class="mt-2 list-decimal list-inside space-y-1.5 text-sm text-coffee-mocha">
                        <template x-for="(step, i) in (recipe?.step_by_step ?? [])" :key="i">
                            <li><span x-text="step"></span></li>
                        </template>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-4 border-t border-coffee-mocha/30 flex flex-col sm:flex-row sm:justify-between gap-3">
            <button type="button" @click="roll(recipe?.id ?? null)"
                    :disabled="loading"
                    class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors disabled:opacity-50">
                &#127922; Roll Another Recipe
            </button>

            <a :href="recipe ? '/recipes/' + recipe.slug : '#'"
               x-show="recipe !== null"
               x-cloak
               class="px-4 py-2 rounded border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors text-center">
                View Full Recipe
            </a>
        </div>
    </div>
</div>
