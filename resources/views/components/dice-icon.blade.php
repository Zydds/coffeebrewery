{{-- Shared dice glyph for the "Surprise me" CTAs (was duplicated inline
     in home hero + both surprise bands). Decorative — always aria-hidden;
     the button/link text carries the label. --}}
@props(['size' => 20])

<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <rect x="3.5" y="3.5" width="17" height="17" rx="3"/>
    <circle cx="8.5" cy="8.5" r="1" fill="currentColor"/>
    <circle cx="15.5" cy="15.5" r="1" fill="currentColor"/>
    <circle cx="12" cy="12" r="1" fill="currentColor"/>
</svg>
