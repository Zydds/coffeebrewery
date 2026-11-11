{{-- Decorative coffee sprig. Illustration only: aria-hidden, no pointer
     events, and hidden below 768px. Position it with the class attribute;
     the host section must be `relative isolate` so the -1 z-index stays
     above that section's background and behind its content. --}}
@props([
    'type' => 'leaf',
    'rotate' => 0,
    'flip' => false,
    'flipY' => false,
    'size' => '',
    'tone' => 'light',
])

@php
    $art = match ($type) {
        'branch' => ['id' => 'cb-sprig-branch', 'viewBox' => '0 0 152 251', 'size' => 'h-64 w-48'],
        'pair' => ['id' => 'cb-sprig-pair', 'viewBox' => '0 0 147 95', 'size' => 'h-28 w-40'],
        default => ['id' => 'cb-sprig-leaf', 'viewBox' => '0 0 77 82', 'size' => 'h-24 w-24'],
    };

    // Transform around the viewBox centre, which is not the origin any more
    // now that each viewBox carries 10px of padding.
    [$minX, $minY, $boxWidth, $boxHeight] = array_map('floatval', explode(' ', $art['viewBox']));

    $transform = [];

    if ($flip) {
        $transform[] = 'translate(' . (2 * $minX + $boxWidth) . ' 0) scale(-1 1)';
    }

    // Vertical mirror inside the viewBox keeps the art inside its box, so a
    // hanging sprig never clips at the tips the way a 180deg rotate would.
    if ($flipY) {
        $transform[] = 'translate(0 ' . (2 * $minY + $boxHeight) . ') scale(1 -1)';
    }

    if ((float) $rotate !== 0.0) {
        $transform[] = 'rotate(' . (float) $rotate . ' ' . ($minX + $boxWidth / 2) . ' ' . ($minY + $boxHeight / 2) . ')';
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
