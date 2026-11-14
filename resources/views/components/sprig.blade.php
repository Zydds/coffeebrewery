{{-- Decorative coffee artwork, used only through x-sprig-side. Illustration
     only: aria-hidden, no pointer events. `flip` mirrors the art horizontally
     for the right-hand side; CSS owns size, rotation and placement. --}}
@props([
    'type' => 'branch',
    'flip' => false,
    'size' => '',
    'tone' => 'light',
])

@php
    $art = match ($type) {
        'pair' => ['id' => 'cb-sprig-pair', 'viewBox' => '0 0 147 95', 'size' => 'h-28 w-40'],
        'leaf' => ['id' => 'cb-sprig-leaf', 'viewBox' => '0 0 77 82', 'size' => 'h-24 w-24'],
        default => ['id' => 'cb-sprig-branch', 'viewBox' => '0 0 152 251', 'size' => 'h-64 w-48'],
    };

    // Mirror inside the viewBox so the art stays inside its box instead of
    // clipping at the tips. The viewBox carries 10px of padding.
    [$minX, , $boxWidth] = array_map('floatval', explode(' ', $art['viewBox']));

    $transform = $flip ? 'translate(' . (2 * $minX + $boxWidth) . ' 0) scale(-1 1)' : '';

    $tone = $tone === 'dark' ? 'dark' : 'light';
@endphp

<svg {{ $attributes->class(['sprig', 'sprig-tone-' . $tone, $size ?: $art['size']]) }}
     viewBox="{{ $art['viewBox'] }}"
     aria-hidden="true"
     focusable="false">
    <g @if ($transform) transform="{{ $transform }}" @endif>
        <use href="#{{ $art['id'] }}" />
    </g>
</svg>
