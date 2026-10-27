@props(['method'])

<article {{ $attributes->merge([
    'class' => 'relative flex flex-col overflow-hidden rounded-2xl border border-coffee-mocha bg-coffee-cream transition-all duration-200 hover:-translate-y-1 hover:border-coffee-amber hover:shadow-[0_10px_24px_rgba(44,26,20,0.15)]',
]) }}
     x-data="{ open: false, touch: matchMedia('(hover: none)').matches }"
     @mouseleave="!touch && (open = false)"
     @keydown.escape.window="open = false">

    <div class="relative grid h-24 sm:h-28 place-items-center overflow-hidden bg-coffee-roast text-coffee-amber shrink-0">
        @if ($method->image_url)
            <img src="{{ $method->image_url }}" alt="{{ $method->name }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
        @else
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 5h14l-4 8H9L5 5z"/>
                <path d="M9 13v3h6v-3"/>
                <path d="M7 20h10"/>
            </svg>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-1.5 p-4">
        <h3 class="font-display text-lg font-bold leading-snug text-coffee-espresso">{{ $method->name }}</h3>
        <p class="text-sm font-medium text-coffee-mocha line-clamp-2">{{ __($method->description) }}</p>

        <div class="mt-auto flex items-center gap-2 pt-2 text-xs font-medium text-coffee-mocha">
            <span>{{ __('Difficulty') }}</span>
            <span class="flex gap-1" aria-hidden="true">
                @php($level = ['easy' => 1, 'medium' => 2, 'hard' => 3][$method->difficulty_level] ?? 1)
                @for ($i = 1; $i <= 3; $i++)
                    <i class="h-2.5 w-2.5 rounded-full border-2 border-coffee-mocha {{ $i <= $level ? 'border-coffee-espresso bg-coffee-espresso' : '' }}"></i>
                @endfor
            </span>
            <span class="sr-only">{{ __('Difficulty') }} {{ __(ucfirst($method->difficulty_level)) }}</span>
        </div>
    </div>

    <div class="px-4 pb-4">
        <button type="button"
                @click.stop="open = !open"
                class="block min-h-[44px] w-full rounded-[10px] bg-coffee-espresso text-sm font-semibold text-coffee-latte transition-colors duration-150 hover:bg-coffee-amber hover:text-coffee-espresso"
                :class="open ? 'invisible opacity-0' : ''"
                aria-expanded="false" :aria-expanded="open.toString()">
            {{ __('Details') }}
        </button>
    </div>

    <div class="absolute inset-0 flex flex-col justify-center gap-2.5 bg-coffee-roast p-5 text-coffee-cream opacity-0 transition-opacity duration-200"
         :class="open ? 'opacity-100' : ''"
         @click="open = false"
         x-cloak>
        <h3 class="font-display text-lg font-bold text-coffee-latte">{{ $method->name }}</h3>

        <p class="text-xs font-semibold uppercase tracking-wider text-coffee-cream/80">{{ __('Equipment') }}</p>
        <div class="flex flex-wrap gap-2">
            @forelse ($method->equipment_needed ?? [] as $item)
                <span class="rounded-full bg-coffee-cream px-3 py-0.5 text-sm text-coffee-espresso">{{ $item }}</span>
            @empty
                <span class="text-sm text-coffee-cream/70">{{ __('None listed') }}</span>
            @endforelse
        </div>

        <p class="text-sm"><b>{{ __('Level') }}</b> {{ __(ucfirst($method->difficulty_level)) }}</p>
        <p class="text-sm"><b>{{ __('Recipes') }}</b> {{ $method->recipes_count ?? $method->recipes()->count() }}</p>

        <a href="{{ route('methods.show', $method->slug) }}"
           class="mt-1 inline-flex min-h-[40px] items-center justify-center rounded-[10px] bg-coffee-amber px-4 text-sm font-semibold text-coffee-espresso hover:bg-coffee-latte transition-colors">
            {{ __('View full details') }}
        </a>
    </div>
</article>
