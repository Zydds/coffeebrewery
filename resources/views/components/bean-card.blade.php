{{-- The single catalog card: one shell, image block, level dots, Details
     overlay and state set for beans AND brew methods. Pass either
     :bean or :method so the two sections cannot drift into two designs.
     Level dots animate from app.css (.level-dot) when the card reveals. --}}
@props(['bean' => null, 'method' => null])

@php
    $item = $bean ?? $method;
    $isMethod = $bean === null;

    // Methods grade easy/medium/hard (3 dots); beans grade 1-5 (5 dots).
    $level = $isMethod
        ? (['easy' => 1, 'medium' => 2, 'hard' => 3][$item->difficulty_level] ?? 1)
        : $item->acidity_level;
    $dotCount = $isMethod ? 3 : 5;
    $levelLabel = $isMethod ? __('Difficulty') : __('Acidity');
    $levelSr = $isMethod
        ? __('Difficulty').' '.__(ucfirst($item->difficulty_level))
        : __('Acidity :level of 5', ['level' => $level]);

    $subtitle = $isMethod
        ? __($item->description)
        : trim($item->origin_country.($item->category ? ', '.$item->category->name : ''));

    $chipLabel = $isMethod ? __('Equipment') : __('Flavor notes');
    $chips = $isMethod ? ($item->equipment_needed ?? []) : ($item->flavor_notes ?? []);

    $lineOne = $isMethod
        ? [__('Level'), __(ucfirst($item->difficulty_level))]
        : [__('Body'), $item->body_level.' '.__('of 5')];

    $lineTwo = $isMethod
        ? [__('Recipes'), $item->recipes_count ?? $item->recipes()->count()]
        : [__('Serve'), implode(', ', $item->recommended_drinks ?? []) ?: '—'];

    // "per 1 pcs" reads wrong, so drop a leading count of one; the stored
    // unit stays as-is ("sachet", "100 gr").
    $priceUnit = trim(preg_replace('/^1\s+/', '', (string) ($item->price_unit ?? ''))) ?: '100 gr';

    $href = $isMethod ? route('methods.show', $item->slug) : route('beans.show', $item->slug);
@endphp

<article {{ $attributes->merge([
    'class' => 'card-lift relative flex flex-col overflow-hidden rounded-2xl border border-coffee-mocha bg-coffee-cream hover-fine:border-coffee-amber hover-fine:shadow-[0_10px_24px_rgba(44,26,20,0.15)] motion-safe:hover-fine:-translate-y-1',
]) }}
     x-data="{
        open: false,
        touch: matchMedia('(hover: none)').matches,
        toggle() {
            this.open = true;
            this.$nextTick(() => this.$refs.panelLink?.focus());
        },
        close(restore = false) {
            if (!this.open) {
                return;
            }

            this.open = false;

            if (restore) {
                this.$nextTick(() => this.$refs.detailsBtn?.focus());
            }
        },
     }"
     @mouseleave="!touch && close()"
     @keydown.escape.window="close(true)">

    <div class="relative grid h-24 sm:h-28 place-items-center overflow-hidden bg-coffee-roast text-coffee-amber shrink-0">
        {{-- Icon block, always rendered: it doubles as the photo fallback.
             A product photo (when present) paints over it, and if that
             photo is missing or 404s it removes itself, leaving the icon. --}}
        @if ($isMethod)
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 5h14l-4 8H9L5 5z"/>
                <path d="M9 13v3h6v-3"/>
                <path d="M7 20h10"/>
            </svg>
        @else
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <ellipse cx="12" cy="12" rx="6" ry="9" transform="rotate(35 12 12)"/>
                <path d="M8 17c4-2 4-8 8-10"/>
            </svg>
        @endif

        @if ($item->image_url)
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                 class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async"
                 onerror="this.remove()">
        @endif

        {{-- The roast block ends in a soft wave so the brown flows
             into the cream body instead of stopping at a hard edge. --}}
        <svg class="pointer-events-none absolute inset-x-0 bottom-0 w-full text-coffee-cream" height="12"
             viewBox="0 0 200 12" preserveAspectRatio="none" fill="currentColor" aria-hidden="true">
            <path d="M0 6 Q25 2 50 6 T100 6 T150 6 T200 6 L200 12 L0 12 Z"/>
        </svg>
    </div>

    <div class="flex flex-1 flex-col gap-1.5 p-4">
        <h3 class="font-display text-lg font-bold leading-snug text-coffee-espresso">{{ $item->name }}</h3>
        <p class="text-sm font-medium text-coffee-mocha{{ $isMethod ? ' line-clamp-2' : '' }}">{{ $subtitle }}</p>

        <div class="mt-auto flex items-center gap-2 pt-2 text-xs font-medium text-coffee-mocha">
            <span>{{ $levelLabel }}</span>
            <span class="flex gap-1" aria-hidden="true">
                @for ($i = 1; $i <= $dotCount; $i++)
                    @php($on = $i <= $level)
                    <i class="level-dot h-2.5 w-2.5 rounded-full border-2 border-coffee-mocha {{ $on ? 'is-on border-coffee-espresso bg-coffee-espresso' : '' }}"
                       @if ($on) style="--d: {{ $i - 1 }}" @endif></i>
                @endfor
            </span>
            <span class="sr-only">{{ $levelSr }}</span>
        </div>

        <div class="mt-1.5 flex flex-wrap items-baseline gap-x-1.5 text-coffee-espresso">
            @if ($isMethod)
                <span class="text-sm font-semibold">{{ $item->recipes_count ?? $item->recipes()->count() }} {{ __('recipes') }}</span>
            @elseif ($item->price !== null)
                <span class="text-[15px] font-bold tabular-nums">Rp. {{ number_format((float) $item->price, 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-coffee-mocha">{{ __('per :unit', ['unit' => $priceUnit]) }}</span>
            @else
                <span class="font-medium text-coffee-mocha">&mdash;</span>
            @endif
        </div>
    </div>

    <div class="px-4 pb-4">
        <button type="button"
                x-ref="detailsBtn"
                @click.stop="toggle()"
                aria-controls="card-panel-{{ $item->id }}"
                :aria-expanded="open.toString()"
                class="block min-h-[44px] w-full rounded-xl bg-coffee-espresso text-sm font-semibold text-coffee-latte transition-[background-color,color,opacity] duration-150 hover:bg-coffee-amber hover:text-coffee-espresso focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-cream"
                :class="open ? 'opacity-0' : ''">
            {{ __('Details') }}
        </button>
    </div>

    {{-- Closed state is `invisible`, not just transparent: an opacity-0
         overlay still hit-tests, and its link sat exactly on top of the
         Details button, so the button could never be clicked. --}}
    <div id="card-panel-{{ $item->id }}" role="group" aria-label="{{ $item->name }}"
         class="invisible absolute inset-0 flex flex-col justify-center gap-2.5 bg-coffee-roast p-5 text-coffee-cream opacity-0 transition-[opacity,visibility] duration-200"
         :class="open ? 'visible opacity-100' : 'invisible opacity-0'"
         @click="close(true)"
         x-cloak>
        <p class="font-display text-lg font-bold text-coffee-latte">{{ $item->name }}</p>

        <p class="text-xs font-semibold uppercase tracking-wider text-coffee-cream/80">{{ $chipLabel }}</p>
        <div class="flex flex-wrap gap-2">
            @forelse ($chips as $chip)
                <span class="rounded-full bg-coffee-cream px-3 py-0.5 text-sm text-coffee-espresso">{{ $chip }}</span>
            @empty
                <span class="text-sm text-coffee-cream/70">{{ __('None listed') }}</span>
            @endforelse
        </div>

        <p class="text-sm"><b>{{ $lineOne[0] }}</b> {{ $lineOne[1] }}</p>
        <p class="text-sm"><b>{{ $lineTwo[0] }}</b> {{ $lineTwo[1] }}</p>

        <a href="{{ $href }}" x-ref="panelLink"
           class="mt-1 inline-flex min-h-[44px] items-center justify-center rounded-xl bg-coffee-amber px-4 text-sm font-semibold text-coffee-espresso transition-colors hover:bg-coffee-latte focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-roast">
            {{ __('View full details') }}
        </a>
    </div>
</article>
