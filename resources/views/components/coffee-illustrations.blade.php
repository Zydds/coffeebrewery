{{-- One inline sprite for the catalog cards. The card renders a <use>
     reference; its whole icon lifts on open (app.css .is-open
     .card-illustration). Per-part motion lives in the large
     coffee-illustration component instead. Each symbol stays under 1.5 KB.
     Included once per page from layouts/public.blade.php. --}}
<svg class="pointer-events-none absolute h-0 w-0 overflow-hidden" aria-hidden="true" focusable="false">
    <symbol id="cb-v60" viewBox="0 0 48 48">
        <path d="M10 12h28L28 26h-8z" fill="currentColor"/>
        <path d="M16 26h16v6a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6z" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
        <path d="M24 38v4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    </symbol>

    <symbol id="cb-french-press" viewBox="0 0 48 48">
        <rect x="14" y="14" width="20" height="24" rx="3" fill="none" stroke="currentColor" stroke-width="2.5"/>
        <path d="M18 22h12M18 28h12M18 34h12" stroke="currentColor" stroke-width="2"/>
        <path d="M24 6v8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        <rect x="20" y="4" width="8" height="4" rx="2" fill="currentColor"/>
    </symbol>

    <symbol id="cb-aeropress" viewBox="0 0 48 48">
        <rect x="16" y="10" width="16" height="24" rx="4" fill="none" stroke="currentColor" stroke-width="2.5"/>
        <path d="M16 22h16" stroke="currentColor" stroke-width="2"/>
        <path d="M24 4v6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        <path d="M12 38h24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    </symbol>

    <symbol id="cb-espresso" viewBox="0 0 48 48">
        <rect x="8" y="12" width="32" height="14" rx="3" fill="none" stroke="currentColor" stroke-width="2.5"/>
        <path d="M18 26v6h12v-6" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
        <path d="M16 38h16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        <path d="M20 8c-2 2 2 4 0 6M28 8c-2 2 2 4 0 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </symbol>

    <symbol id="cb-sachet" viewBox="0 0 48 48">
        <path d="M14 12h20l-2 20H16z" fill="currentColor"/>
        <path d="M14 12l4-4h12l4 4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
        <path d="M20 22h8" stroke="var(--color-coffee-cream)" stroke-width="2.5" stroke-linecap="round"/>
        <path d="M24 36v6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    </symbol>

    <symbol id="cb-iced" viewBox="0 0 48 48">
        <path d="M14 8h20l-3 32H17z" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
        <path d="M18 14h12l-2 18H20z" fill="currentColor" opacity=".35"/>
        <path d="M30 6l5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        <rect x="22" y="18" width="6" height="6" rx="1" fill="currentColor" opacity=".75"/>
    </symbol>

    <symbol id="cb-bean" viewBox="0 0 48 48">
        <ellipse cx="24" cy="24" rx="11" ry="18" transform="rotate(32 24 24)" fill="currentColor"/>
        <path d="M18 36c7-4 6-16 12-24" fill="none" stroke="var(--color-coffee-cream)" stroke-width="2.5" stroke-linecap="round"/>
    </symbol>

    <symbol id="cb-close" viewBox="0 0 24 24">
        <path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    </symbol>

    {{-- Decorative sprigs. Illustration-only: the two --leaf-* tokens
         never appear on UI elements. One leaf path, repeated at different
         angles, keeps all three symbols original and light. --}}
    <symbol id="cb-sprig-branch" viewBox="0 0 150 210">
        <path d="M62 204C56 166 72 132 64 96 60 66 48 40 34 22" fill="none" stroke="var(--leaf-dark)" stroke-width="4.5" stroke-linecap="round"/>
        <path d="M64 150c6 10 10 18 16 28M66 142c12 6 22 8 30 10" fill="none" stroke="var(--leaf-dark)" stroke-width="2.5" stroke-linecap="round"/>
        <g>
            <g transform="translate(34 22) rotate(-140) scale(.9)">
                <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
                <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
            </g>
            <g transform="translate(66 78) rotate(-28)">
                <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
                <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
            </g>
            <g transform="translate(44 54) rotate(-152) scale(.92)">
                <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
                <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
            </g>
            <g transform="translate(58 108) rotate(166) scale(.86)">
                <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
                <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
            </g>
            <g transform="translate(68 132) rotate(-6) scale(.9)">
                <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
                <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
            </g>
        </g>
        <circle cx="82" cy="180" r="10" fill="var(--color-coffee-amber)"/>
        <circle cx="99" cy="156" r="9" fill="var(--color-coffee-amber)"/>
    </symbol>

    <symbol id="cb-sprig-pair" viewBox="0 0 120 84">
        <path d="M18 78C36 64 52 50 74 32" fill="none" stroke="var(--leaf-dark)" stroke-width="3.5" stroke-linecap="round"/>
        <g transform="translate(38 60) rotate(-155) scale(1.02)">
            <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
            <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
        </g>
        <g transform="translate(74 33) rotate(-25) scale(1.02)">
            <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
            <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
        </g>
    </symbol>

    <symbol id="cb-sprig-leaf" viewBox="0 0 84 84">
        <path d="M24 70C28 64 32 58 36 52" fill="none" stroke="var(--leaf-dark)" stroke-width="3" stroke-linecap="round"/>
        <g transform="translate(34 52) rotate(-52) scale(1.15)">
            <path d="M0 0C10-13 30-15 44 0 30 15 10 13 0 0Z" fill="var(--leaf-dark)"/>
            <path d="M6 0C17-3 29-3 40 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.6" stroke-linecap="round"/>
        </g>
    </symbol>
</svg>
