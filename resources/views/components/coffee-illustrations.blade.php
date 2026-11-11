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
         never appear on UI elements. The leaf is defined once here and
         reused by all three symbols, so the shape is identical everywhere
         and the branch keeps its budget. Filled ribbons give the stems a
         real taper. --}}
    <defs>
        <g id="cb-sprig-leaf-art">
            <path d="M0 0C6-5.4 14-9.3 22-9.3C32-8 41.6-1.6 44 0C41.6 1.6 32 8 22 9.3C14 9.3 6 5.4 0 0Z" fill="var(--leaf-dark)"/>
            <path d="M6 0C16-1.5 29-1.5 39 0" fill="none" stroke="var(--leaf-light)" stroke-width="1.9" stroke-linecap="round"/>
        </g>
    </defs>

    <symbol id="cb-sprig-branch" viewBox="0 0 152 251">
        <g transform="translate(-64.3 2.4)">
            <path d="M145.3 236.2L144.2 221.5L142.7 206.9L141 192.4L139 177.9L136.7 163.5L134 149.1L131.1 134.8L127.9 120.5L124.3 106.3L120.5 92.2L116.3 78.1L111.9 64.1L107.1 50.1L102 36.1L96.7 22.3L91 8.4L93 7.6L99 21.3L104.7 35.1L110.1 49L115.1 62.9L119.9 77L124.3 91.1L128.4 105.2L132.1 119.5L135.6 133.8L138.7 148.2L141.5 162.6L144 177.1L146.2 191.7L148 206.3L149.5 221L150.7 235.8Z" fill="var(--leaf-dark)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(148.5 225.3) rotate(194) scale(1.44)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(144.6 211.4) rotate(-23) scale(1.4)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(143 178.5) rotate(201) scale(1.34)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(138.3 166.1) rotate(-16) scale(1.3)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(134.2 132.3) rotate(192) scale(1.24)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(129 121.4) rotate(-26) scale(1.2)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(124.3 93.5) rotate(199) scale(1.12)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(119.1 85.2) rotate(-18) scale(1.08)"/>
            <path d="M114.6 66.9L123.2 68.5M114.6 66.9L120.5 60.4M114.6 66.9L108.2 69M108.4 49L102.7 55.6M108.4 49L99.8 47.6M102.3 32.7L110.9 33.9M102.3 32.7L107.8 26M102.3 32.7L95.9 35.2M96.5 18.6L91.1 25.4M96.5 18.6L87.8 17.6" fill="none" stroke="var(--leaf-dark)" stroke-width="1.7"/>
            <g fill="var(--color-coffee-amber)"><circle cx="125.8" cy="68.9" r="7"/><circle cx="122.3" cy="58.5" r="6.6"/><circle cx="105.7" cy="69.8" r="6.4"/><circle cx="101" cy="57.5" r="6.8"/><circle cx="97.2" cy="47.2" r="6.4"/><circle cx="113.5" cy="34.2" r="6.6"/><circle cx="109.5" cy="24" r="6.2"/><circle cx="93.5" cy="36.2" r="6.1"/><circle cx="89.5" cy="27.5" r="6.5"/><circle cx="85.3" cy="17.3" r="6.1"/></g>
            <g fill="var(--color-coffee-cream)" opacity=".5"><ellipse cx="127.8" cy="70.5" rx="1.9" ry="1.2"/><ellipse cx="124.4" cy="60" rx="1.9" ry="1.2"/><ellipse cx="107.8" cy="71.3" rx="1.9" ry="1.2"/><ellipse cx="98.9" cy="60.5" rx="1.9" ry="1.2"/><ellipse cx="95.1" cy="50.1" rx="1.9" ry="1.2"/><ellipse cx="115.5" cy="35.6" rx="1.9" ry="1.2"/><ellipse cx="111.5" cy="25.4" rx="1.9" ry="1.2"/><ellipse cx="95.5" cy="37.6" rx="1.9" ry="1.2"/><ellipse cx="87.5" cy="30.5" rx="1.9" ry="1.2"/><ellipse cx="83.2" cy="20.4" rx="1.9" ry="1.2"/></g>
            <g fill="var(--color-coffee-espresso)" opacity=".6"><circle cx="123.2" cy="68.5" r="1.1"/><circle cx="120.5" cy="60.4" r="1.1"/><circle cx="108.2" cy="69" r="1.1"/><circle cx="102.7" cy="55.6" r="1.1"/><circle cx="99.8" cy="47.6" r="1.1"/><circle cx="110.9" cy="33.9" r="1.1"/><circle cx="107.8" cy="26" r="1.1"/><circle cx="95.9" cy="35.2" r="1.1"/><circle cx="91.1" cy="25.4" r="1.1"/><circle cx="87.8" cy="17.6" r="1.1"/></g>
        </g>
    </symbol>

    <symbol id="cb-sprig-pair" viewBox="0 0 147 95">
        <g transform="translate(3 -6)">
            <path d="M24 90C42 80 64 64 93.2 41.6" fill="none" stroke="var(--leaf-dark)" stroke-width="3.4" stroke-linecap="round"/>
            <use href="#cb-sprig-leaf-art" transform="translate(48 76) rotate(-150) scale(.95)"/>
            <use href="#cb-sprig-leaf-art" transform="translate(90 44) rotate(-25)"/>
        </g>
    </symbol>

    <symbol id="cb-sprig-leaf" viewBox="0 0 77 82">
        <g transform="translate(-18 1)">
            <path d="M28 70C34 63 40 57 49 46.5" fill="none" stroke="var(--leaf-dark)" stroke-width="2.2" stroke-linecap="round"/>
            <use href="#cb-sprig-leaf-art" transform="translate(46 50) rotate(-48) scale(1.05)"/>
        </g>
    </symbol>
</svg>
