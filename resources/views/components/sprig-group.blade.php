{{-- A decorative sprig cluster: two or three pieces from one preset, placed
     as a single anchored unit instead of loose sprigs. The preset owns the
     rotation, flip, tone and size slot; app.css owns the anchor and the
     clamp() sizes. Illustration only: aria-hidden, no pointer events, hidden
     below 768px, and the host section must be `relative isolate`. --}}
@props(['variant' => 'hero'])

@php
    // Slots map to `.sprig-piece--a|b|c` in app.css. Rotations stay inside
    // -35deg..35deg and every group mixes at least two of them.
    $presets = [
        'hero' => [
            ['slot' => 'a', 'type' => 'pair', 'rotate' => 3, 'flip' => true],
            ['slot' => 'b', 'type' => 'leaf', 'rotate' => -12],
            ['slot' => 'c', 'type' => 'leaf', 'rotate' => 16],
        ],
        'methods' => [
            ['slot' => 'a', 'type' => 'pair', 'rotate' => -10, 'flip' => true],
            ['slot' => 'b', 'type' => 'leaf', 'rotate' => 14],
        ],
        'surprise' => [
            ['slot' => 'a', 'type' => 'leaf', 'rotate' => -6, 'flip' => true, 'tone' => 'dark'],
            ['slot' => 'b', 'type' => 'leaf', 'rotate' => 8, 'tone' => 'dark'],
        ],
    ];

    $pieces = $presets[$variant] ?? $presets['hero'];
@endphp

<div {{ $attributes->class(['sprig-group', 'sprig-group--' . $variant]) }}>
    @foreach ($pieces as $piece)
        <x-sprig :type="$piece['type']"
                 :rotate="$piece['rotate']"
                 :flip="$piece['flip'] ?? false"
                 :tone="$piece['tone'] ?? 'light'"
                 :size="'sprig-piece sprig-piece--' . $piece['slot']" />
    @endforeach
</div>
