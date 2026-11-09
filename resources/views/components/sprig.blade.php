{{-- Decorative coffee sprig. Illustration only: aria-hidden, no pointer
     events, and hidden below 768px. Position it with the class attribute;
     the host section must be `relative isolate` so the -1 z-index stays
     above that section's background and behind its content. --}}
@props([
    'type' => 'leaf',
    'rotate' => 0,
    'flip' => false,
    'size' => '',
    'tone' => 'light',
])

@php
    $art = match ($type) {
        'branch' => ['id' => 'cb-sprig-branch', 'viewBox' => '0 0 150 210', 'width' => 150, 'height' => 210, 'size' => 'h-64 w-48'],
        'pair' => ['id' => 'cb-sprig-pair', 'viewBox' => '0 0 120 84', 'width' => 120, 'height' => 84, 'size' => 'h-28 w-40'],
        default => ['id' => 'cb-sprig-leaf', 'viewBox' => '0 0 84 84', 'width' => 84, 'height' => 84, 'size' => 'h-24 w-24'],
    };

    $transform = [];

    if ($flip) {
        $transform[] = 'translate(' . $art['width'] . ' 0) scale(-1 1)';
    }

    if ((float) $rotate !== 0.0) {
        $transform[] = 'rotate(' . (float) $rotate . ' ' . ($art['width'] / 2) . ' ' . ($art['height'] / 2) . ')';
    }

    $tone = $tone === 'dark' ? 'dark' : 'light';
@endphp

<svg {{ $attributes->class(['sprig', 'sprig-tone-' . $tone, $size ?: $art['size']]) }}
     viewBox="{{ $art['viewBox'] }}"
     aria-hidden="true"
     focusable="false">
    <g @if ($transform) transform="{{ implode(' ', $transform) }}" @endif>
        <use href="#{{ $art['id'] }}" />
    </g>
</svg>
