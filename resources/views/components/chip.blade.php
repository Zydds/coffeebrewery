{{-- One filter chip for every facet row on the site (bean categories,
     recipe flags/methods, sachet categories). Replaces the inline active
     ternary that was copy-pasted at each call site.
     Motion per strategy: 150ms color, 120ms press (transform only,
     motion-safe), focus-visible ring at parity with buttons.
     hover-fine: touch gets the press state, not a sticky invert. --}}
@props(['active' => false, 'href' => '#'])

<a {{ $attributes->merge([
        'href' => $href,
        'aria-current' => $active ? 'page' : false,
        'class' => 'relative z-10 min-h-[44px] px-5 inline-flex items-center rounded-full font-semibold text-[15px] '
            . 'transition-[background-color,color,transform] duration-150 ease-out '
            . 'motion-safe:active:scale-[0.97] '
            . 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink '
            . ($active
                ? 'bg-coffee-espresso text-coffee-latte'
                : 'bg-coffee-cream text-coffee-espresso hover-fine:bg-coffee-espresso hover-fine:text-coffee-latte'),
    ]) }}>
    {{ $slot }}
</a>
