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
</svg>
