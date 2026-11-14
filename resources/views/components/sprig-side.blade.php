{{-- One branch that enters from the viewport edge, as if it grew from
     outside the screen. The 0x0 anchor sits on the edge at `top` (% of the
     parent section); the art is rotated around its stem base so it runs
     horizontally with a 10deg droop. Only the cut stem is cropped, and the
     cluster stays inside its section's free band. app.css owns the size,
     rotation and one-shot sway. Decorative only: aria-hidden, no pointer
     events, hidden below 1280. --}}
@props([
    'side' => 'right',
    'top' => 50,
    'tone' => 'light',
    'scale' => 1,
])

@php
    $side = $side === 'left' ? 'left' : 'right';
    $mirror = $side === 'right';
@endphp

<div {{ $attributes->class(['sprig-side', 'sprig-side--' . $side]) }}
     style="top: {{ (float) $top }}%; --sprig-scale: {{ (float) $scale }}">
    <x-sprig type="branch" :flip="$mirror" :tone="$tone" size="sprig-side__art" />
</div>
