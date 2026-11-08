{{-- Shared dashed empty state (catalog, brew methods, recipes).
      Message = default slot; the optional action is a persistent underline
      so it reads as a link without relying on colour alone, and mocha
      passes AA on both latte (7.2:1) and cream (6.3:1). --}}
@props(['action' => null, 'href' => null])

<div {{ $attributes->merge(['class' => 'col-span-full rounded-2xl border border-dashed border-coffee-mocha/50 p-10 text-center']) }}>
    <p class="text-sm text-coffee-mocha">{{ $slot }}</p>
    @if ($action)
        <a href="{{ $href }}"
           class="mt-3 inline-block rounded text-sm font-semibold text-coffee-mocha underline decoration-coffee-mocha/50 underline-offset-2 hover:text-coffee-espresso hover:decoration-coffee-espresso focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
            {{ $action }}
        </a>
    @endif
</div>
