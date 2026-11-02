{{-- Flat "flowing water" section divider — one inline single-path wave.
     from-color = the section ABOVE (svg background, shows above the wave),
     to-color = the section BELOW (fills the wave path, continues into it).
     Both are whitelist-checked against the 7 existing coffee-* tokens, so
     no new hex can ever enter through this component; accepts "cream" or
     "coffee-cream". flip mirrors the wave left-right (direction itself is
     expressed by swapping from/to, not by flipping). height is the band
     thickness — number = px, or any CSS length. Decorative only.
     w-full + display:block on the svg are load-bearing: they stretch the
     viewBox to the container and remove the inline descender gap. --}}
@props([
    'fromColor' => 'latte',
    'toColor' => 'cream',
    'flip' => false,
    'height' => 96,
])

@php
    $palette = ['espresso', 'mocha', 'latte', 'amber', 'amber-ink', 'cream', 'roast'];
    $token = fn ($name, $fallback) => in_array($n = preg_replace('/^coffee-/', '', (string) $name), $palette, true) ? $n : $fallback;
    $from = $token($fromColor, 'latte');
    $to = $token($toColor, 'cream');
    $flip = filter_var($flip, FILTER_VALIDATE_BOOLEAN);
    $band = is_numeric($height) ? $height . 'px' : $height;
@endphp

<svg {{ $attributes->merge([
        'class' => 'block w-full',
        'style' => 'height:' . $band . ';background-color:var(--color-coffee-' . $from . ');'
            . ($flip ? 'transform:scaleX(-1);' : ''),
    ]) }}
     viewBox="0 0 1440 100"
     preserveAspectRatio="none"
     aria-hidden="true">
    <path d="M0,45 C240,85 480,10 720,40 C960,75 1200,15 1440,45 L1440,100 L0,100 Z"
          style="fill:var(--color-coffee-{{ $to }});"></path>
</svg>
