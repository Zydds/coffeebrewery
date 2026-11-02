{{-- Shared dashed empty state (catalog, brew methods, recipes).
     Message = default slot; optional action link uses amber-ink so the
     "Clear search" text passes AA on latte (was amber, 3.1:1). --}}
@props(['action' => null, 'href' => null])

<div {{ $attributes->merge(['class' => 'col-span-full rounded-2xl border border-dashed border-coffee-mocha/50 p-10 text-center']) }}>
    <p class="text-sm text-coffee-mocha">{{ $slot }}</p>
    @if ($action)
        <a href="{{ $href }}"
           class="mt-3 inline-block rounded text-sm font-semibold text-coffee-amber-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
            {{ $action }}
        </a>
    @endif
</div>
