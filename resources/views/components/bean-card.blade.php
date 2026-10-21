@props(['bean'])

<div class="group relative overflow-hidden rounded-lg border border-coffee-mocha/30 bg-coffee-cream min-h-[260px] cursor-pointer select-none"
     x-data="{ open: false }"
     @click="open = !open">
    <div class="p-5">
        <div class="flex items-start justify-between gap-3">
            <h3 class="text-lg font-semibold text-coffee-espresso leading-snug">
                {{ $bean->name }}
            </h3>
            <span class="shrink-0 text-xs font-medium px-2 py-1 rounded-full bg-coffee-mocha/15 text-coffee-mocha">
                {{ $bean->category->name ?? 'Uncategorized' }}
            </span>
        </div>

        <p class="mt-3 text-sm text-coffee-mocha">
            Origin: <span class="font-medium text-coffee-espresso">{{ $bean->origin_country }}</span>
        </p>

        <p class="mt-2 text-sm text-coffee-mocha">
            Acidity:
            <span class="text-coffee-amber tracking-wider" aria-label="Acidity {{ $bean->acidity_level }} out of 5">
                {{ str_repeat('★', $bean->acidity_level) }}{{ str_repeat('☆', 5 - $bean->acidity_level) }}
            </span>
        </p>

        <p class="mt-4 text-xs text-coffee-mocha/80">Hover or tap for details</p>
    </div>

    {{-- Overlay: fixed over the card so the card height never changes --}}
    <div class="absolute inset-0 p-5 bg-coffee-roast/97 text-coffee-latte rounded-lg transition-opacity duration-200 opacity-0 group-hover:opacity-100"
         :class="open ? 'opacity-100' : ''"
         x-cloak
         @click.stop>
        <div class="h-full flex flex-col">
            <div class="flex items-start justify-between gap-3">
                <h3 class="text-lg font-semibold leading-snug">{{ $bean->name }}</h3>
                <button type="button" @click="open = false" class="shrink-0 p-1 rounded hover:bg-white/10 transition-colors" aria-label="Close overlay">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <dl class="mt-3 space-y-2 text-sm flex-1">
                <div>
                    <dt class="text-coffee-latte/70">Origin</dt>
                    <dd class="font-medium">{{ $bean->origin_country }}</dd>
                </div>

                <div>
                    <dt class="text-coffee-latte/70">Flavor Notes</dt>
                    <dd class="flex flex-wrap gap-1.5 mt-1">
                        @forelse ($bean->flavor_notes ?? [] as $note)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-coffee-amber/25 text-coffee-latte">{{ $note }}</span>
                        @empty
                            <span class="text-xs text-coffee-latte/60">None listed</span>
                        @endforelse
                    </dd>
                </div>

                <div>
                    <dt class="text-coffee-latte/70">Body</dt>
                    <dd class="text-coffee-amber tracking-wider" aria-label="Body {{ $bean->body_level }} out of 5">
                        {{ str_repeat('★', $bean->body_level) }}{{ str_repeat('☆', 5 - $bean->body_level) }}
                    </dd>
                </div>

                <div>
                    <dt class="text-coffee-latte/70">Recommended Drinks</dt>
                    <dd class="font-medium">
                        @forelse ($bean->recommended_drinks ?? [] as $drink)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-white/10 mr-1.5">{{ $drink }}</span>
                        @empty
                            <span class="text-xs text-coffee-latte/60">None listed</span>
                        @endforelse
                    </dd>
                </div>
            </dl>

            <a href="{{ route('beans.show', $bean->slug) }}"
               class="mt-3 inline-flex justify-center items-center gap-1 text-sm font-semibold text-coffee-espresso bg-coffee-amber hover:bg-coffee-amber/90 rounded px-3 py-2 transition-colors">
                View full details &rarr;
            </a>
        </div>
    </div>
</div>
