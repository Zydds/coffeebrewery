{{-- Large flat-vector illustration used only where one is visible at a
     time: the brew-method picker and the expanded card. The parts carry
     .steam / .drip / .plunger / .ice classes, so app.css can play a
     one-shot animation when the element is inserted. --}}
@props(['type' => 'bean', 'tone' => 'amber', 'size' => 'h-40 w-40'])

<svg viewBox="0 0 120 120"
     class="coffee-illustration {{ $size }} {{ $tone === 'amber-ink' ? 'text-coffee-amber-ink' : 'text-coffee-amber' }}"
     role="img" aria-hidden="true">
    @switch($type)
        @case('v60')
            <path d="M22 26h76L72 60H48z" fill="currentColor"/>
            <path d="M40 60h40v14a20 20 0 0 1-20 20 20 20 0 0 1-20-20z" fill="none" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
            <path d="M60 60v22" class="pour" stroke="var(--color-coffee-cream)" stroke-width="5" stroke-linecap="round"/>
            <circle cx="60" cy="98" r="3" class="drip" fill="currentColor"/>
            <path d="M68 18c-4 6 4 10 0 16M80 18c-4 6 4 10 0 16" class="steam" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            @break

        @case('french-press')
            <rect x="34" y="30" width="52" height="62" rx="8" fill="none" stroke="currentColor" stroke-width="5"/>
            <path d="M44 50h32M44 62h32M44 74h32" stroke="currentColor" stroke-width="4"/>
            <g class="plunger">
                <rect x="48" y="24" width="24" height="8" rx="4" fill="currentColor"/>
                <path d="M60 8v16" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
            </g>
            <path d="M44 100h32" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
            @break

        @case('aeropress')
            <rect x="38" y="24" width="44" height="58" rx="8" fill="none" stroke="currentColor" stroke-width="5"/>
            <path d="M38 48h44" stroke="currentColor" stroke-width="4"/>
            <g class="plunger">
                <rect x="46" y="16" width="28" height="8" rx="4" fill="currentColor"/>
                <path d="M60 4v12" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
            </g>
            <path d="M30 98h60" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
            <path d="M46 98v-10h28v10" fill="none" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
            @break

        @case('espresso')
            <rect x="18" y="28" width="84" height="34" rx="6" fill="none" stroke="currentColor" stroke-width="5"/>
            <path d="M42 62v14h36V62" fill="none" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
            <path d="M38 88h44" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
            <path d="M44 18c-4 6 4 10 0 16M60 16c-4 6 4 10 0 16M76 18c-4 6 4 10 0 16" class="steam" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            <circle cx="60" cy="70" r="3" class="drip" fill="currentColor"/>
            @break

        @case('sachet')
            <path d="M34 26h52l-5 50H39z" fill="currentColor"/>
            <path d="M34 26l10-10h32l10 10" fill="none" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
            <path d="M48 50h24" stroke="var(--color-coffee-cream)" stroke-width="5" stroke-linecap="round"/>
            <path d="M60 86v18" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
            <path d="M48 16c-4 6 4 10 0 16M72 16c-4 6 4 10 0 16" class="steam" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            @break

        @case('iced')
            <path d="M32 18h56l-8 86H40z" fill="none" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
            <path d="M42 34h36l-5 48H47z" fill="currentColor" opacity=".35"/>
            <path d="M76 14l14-12" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
            <rect x="50" y="46" width="18" height="16" rx="3" class="ice" fill="currentColor" opacity=".8"/>
            <circle cx="64" cy="72" r="4" class="drip" fill="currentColor"/>
            @break

        @default
            <ellipse cx="60" cy="62" rx="30" ry="48" transform="rotate(32 60 62)" fill="currentColor"/>
            <path d="M44 96c20-12 16-44 34-68" fill="none" stroke="var(--color-coffee-cream)" stroke-width="6" stroke-linecap="round"/>
            <path d="M42 28c-5 8 5 13 0 21M76 24c-5 8 5 13 0 21" class="steam" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
    @endswitch
</svg>
