@props(['bean'])

<article {{ $attributes->merge([
    'class' => 'relative flex flex-col overflow-hidden rounded-2xl border border-coffee-mocha bg-coffee-cream transition-all duration-200 hover:-translate-y-1 hover:border-coffee-amber hover:shadow-[0_10px_24px_rgba(44,26,20,0.15)]',
]) }}
     x-data="{ open: false, touch: matchMedia('(hover: none)').matches }"
     @mouseleave="!touch && (open = false)"
     @keydown.escape.window="open = false">

    <div class="relative grid h-24 sm:h-28 place-items-center overflow-hidden bg-coffee-roast text-coffee-amber shrink-0">
        @if ($bean->image_url)
            <img src="{{ $bean->image_url }}" alt="{{ $bean->name }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
        @else
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <ellipse cx="12" cy="12" rx="6" ry="9" transform="rotate(35 12 12)"/>
                <path d="M8 17c4-2 4-8 8-10"/>
            </svg>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-1.5 p-4">
        <h3 class="font-display text-lg font-bold leading-snug text-coffee-espresso">{{ $bean->name }}</h3>
        <p class="text-sm font-medium text-coffee-mocha">
            {{ $bean->origin_country }}{{ $bean->category ? ', '.$bean->category->name : '' }}
        </p>

        <div class="mt-auto flex items-center gap-2 pt-2 text-xs font-medium text-coffee-mocha">
            <span>Acidity</span>
            <span class="flex gap-1" aria-hidden="true">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="h-2.5 w-2.5 rounded-full border-2 border-coffee-mocha {{ $i <= $bean->acidity_level ? 'border-coffee-espresso bg-coffee-espresso' : '' }}"></i>
                @endfor
            </span>
            <span class="sr-only">Acidity {{ $bean->acidity_level }} of 5</span>
        </div>

        <div class="mt-1.5 text-sm font-semibold text-coffee-espresso">
            @if ($bean->price !== null)
                Rp. {{ number_format((float) $bean->price, 0, ',', '.') }} / {{ $bean->price_unit ?: '100 gr' }}
            @else
                <span class="font-medium text-coffee-mocha">&mdash;</span>
            @endif
        </div>
    </div>

    <div class="px-4 pb-4">
        <button type="button"
                @click.stop="open = !open"
                class="block min-h-[44px] w-full rounded-[10px] bg-coffee-espresso text-sm font-semibold text-coffee-latte transition-colors duration-150 hover:bg-coffee-amber hover:text-coffee-espresso"
                :class="open ? 'invisible opacity-0' : ''"
                aria-expanded="false" :aria-expanded="open.toString()">
            Details
        </button>
    </div>

    <div class="absolute inset-0 flex flex-col justify-center gap-2.5 bg-coffee-roast p-5 text-coffee-cream opacity-0 transition-opacity duration-200"
         :class="open ? 'opacity-100' : ''"
         @click="open = false"
         x-cloak>
        <h3 class="font-display text-lg font-bold text-coffee-latte">{{ $bean->name }}</h3>

        <p class="text-xs font-semibold uppercase tracking-wider text-coffee-cream/80">Flavor notes</p>
        <div class="flex flex-wrap gap-2">
            @forelse ($bean->flavor_notes ?? [] as $note)
                <span class="rounded-full bg-coffee-cream px-3 py-0.5 text-sm text-coffee-espresso">{{ $note }}</span>
            @empty
                <span class="text-sm text-coffee-cream/70">None listed</span>
            @endforelse
        </div>

        <p class="text-sm"><b>Body</b> {{ $bean->body_level }} of 5</p>
        <p class="text-sm"><b>Serve</b>
            @php($serve = implode(', ', $bean->recommended_drinks ?? []))
            {{ $serve !== '' ? $serve : '—' }}
        </p>

        <a href="{{ route('beans.show', $bean->slug) }}"
           class="mt-1 inline-flex min-h-[40px] items-center justify-center rounded-[10px] bg-coffee-amber px-4 text-sm font-semibold text-coffee-espresso hover:bg-coffee-latte transition-colors">
            View full details
        </a>
    </div>
</article>
