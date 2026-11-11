{{-- One sprig cluster that hangs from the top edge of a section, in the
     side gutter, as if it grew from behind the element above it (navbar,
     stats wave, marquee). The root sits above the section line and is
     covered by that element; the pieces tilt away from the content with
     CSS transforms, because rotating 180deg inside the viewBox would clip
     the tips. The group is a 0x0 anchor: the pieces size and hang from it,
     and the whole cluster swings 2deg around the anchor once on entry
     (app.js + app.css). Decorative only: aria-hidden, no pointer events,
     hidden below 1024/1440 by variant. --}}
@props([
    'side' => 'left',
    'tone' => 'light',
    'variant' => 'hero',
])

@php
    $side = $side === 'right' ? 'right' : 'left';
    $mirror = $side === 'right';

    // Second piece: a leaf pair where the gutter is wide, a single leaf
    // where the cluster needs to stay narrow. Both are 70-85% of the branch.
    $second = in_array($variant, ['hero', 'catalog', 'recipes', 'custom'], true) ? 'pair' : 'leaf';
@endphp

<div {{ $attributes->class(['sprig-hanging', 'sprig-hanging--' . $side, 'sprig-hanging--' . $variant]) }}>
    <x-sprig type="branch" :flip="$mirror" flip-y :tone="$tone" size="sprig-piece sprig-piece--a sprig-piece--branch" />
    <x-sprig :type="$second" :flip="$mirror" flip-y :tone="$tone" size="sprig-piece sprig-piece--b sprig-piece--{{ $second }}" />
</div>
