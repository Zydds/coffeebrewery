{{-- The single catalog card: one shell, image block, level dots, Details
     overlay and state set for beans AND brew methods. Pass either
     :bean or :method so the two sections cannot drift into two designs.
     Level dots animate from app.css (.level-dot) when the card reveals. --}}
@props(['bean' => null, 'method' => null, 'featured' => false])

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

    // Sprite id shared with the large picker/expanded drawing.
    $illustration = $item->illustration();
@endphp

<article {{ $attributes->merge([
    'class' => 'card-lift relative flex flex-col overflow-hidden rounded-2xl border border-coffee-mocha bg-coffee-cream hover-fine:border-coffee-amber hover-fine:shadow-[0_10px_24px_rgba(44,26,20,0.15)] motion-safe:hover-fine:-translate-y-1',
]) }}
     x-data="{
        open: false,
        touch: matchMedia('(hover: none)').matches,
        frozen: [],
        releaseTimer: null,
        toggle() {
            clearTimeout(this.releaseTimer);

            if (this.open) {
                this.close();
                return;
            }

            // Pin every sibling at its resting height before the row grows,
            // so the open card is the only one that changes size.
            this.frozen.forEach((el) => {
                el.style.height = '';
            });

            this.frozen = [...this.$root.parentElement.children]
                .filter((el) => el !== this.$root && el.tagName === 'ARTICLE');

            this.frozen.forEach((el) => {
                el.style.height = el.getBoundingClientRect().height + 'px';
            });

            this.open = true;
        },
        close() {
            if (!this.open) {
                return;
            }

            this.open = false;
            clearTimeout(this.releaseTimer);
            this.releaseTimer = setTimeout(() => {
                this.frozen.forEach((el) => el.style.height = '');
                this.frozen = [];
            }, 340);
        },
     }"
     :class="open ? 'is-open' : ''"
     @mouseleave="!touch && close()"
     @keydown.escape.window="close()">

    <div class="relative grid {{ $featured ? 'h-40 lg:h-56' : 'h-24 sm:h-28' }} place-items-center overflow-hidden bg-coffee-roast text-coffee-amber shrink-0">
        {{-- Icon block, always rendered: it doubles as the photo fallback.
             A product photo (when present) paints over it, and if that
             photo is missing or 404s it removes itself, leaving the icon.
             The symbol comes from the page sprite, so the card carries no
             per-item SVG markup. --}}
        <svg class="card-illustration {{ $featured ? 'h-20 w-20 lg:h-28 lg:w-28' : 'h-14 w-14' }}" viewBox="0 0 48 48" aria-hidden="true">
            <use href="#cb-{{ $illustration }}" />
        </svg>

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

    <div class="flex flex-1 flex-col gap-1.5 {{ $featured ? 'p-5 lg:p-6' : 'p-4' }}">
        @if ($featured)
            <p class="eyebrow">{{ __('Featured bean') }}</p>
        @endif
        <h3 class="font-display font-bold leading-snug text-coffee-espresso {{ $featured ? 'text-display tracking-tight' : 'text-lg' }}">{{ $item->name }}</h3>
        <p class="text-sm font-medium text-coffee-mocha{{ $isMethod ? ' line-clamp-2' : '' }}{{ $featured ? ' text-base' : '' }}">{{ $subtitle }}</p>

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
                @click.stop="toggle()"
                aria-controls="card-panel-{{ $item->id }}"
                :aria-expanded="open.toString()"
                class="flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl bg-coffee-espresso text-sm font-semibold text-coffee-latte transition-colors duration-150 hover:bg-coffee-amber hover:text-coffee-espresso focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-cream">
            <span x-show="!open">{{ __('Details') }}</span>
            <span x-show="open" x-cloak>{{ __('Close details') }}</span>
            <svg x-show="open" x-cloak class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                <use href="#cb-close" />
            </svg>
        </button>
    </div>

    {{-- Expansion is in flow, not an overlay: 0fr → 1fr on the grid row
         grows the card itself and nothing else. The closed container is
         hidden (not just clipped) so its link cannot take focus. --}}
    <div id="card-panel-{{ $item->id }}" role="group" aria-label="{{ $item->name }}"
         :aria-hidden="(!open).toString()"
         class="card-expand" :class="open ? 'is-open' : ''">
        <div class="card-expand-inner">
            <div class="relative flex flex-col gap-2.5 border-t border-coffee-cream/15 bg-coffee-roast p-4 text-coffee-cream">
                <button type="button"
                        x-show="touch"
                        x-cloak
                        @click.stop="close()"
                        class="absolute right-2 top-2 grid h-9 w-9 place-items-center rounded-full text-coffee-cream transition-colors hover:bg-coffee-cream/15 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-roast"
                        aria-label="{{ __('Close details') }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                        <use href="#cb-close" />
                    </svg>
                </button>

                <template x-if="open">
                    <x-coffee-illustration :type="$illustration" size="mx-auto h-16 w-16 sm:h-20 sm:w-20" />
                </template>

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

                <a href="{{ $href }}"
                   class="mt-1 inline-flex min-h-[44px] items-center justify-center rounded-xl bg-coffee-amber px-4 text-sm font-semibold text-coffee-espresso transition-colors hover:bg-coffee-latte focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-latte focus-visible:ring-offset-2 focus-visible:ring-offset-coffee-roast">
                    {{ __('View full details') }}
                </a>
            </div>
        </div>
    </div>
</article>
