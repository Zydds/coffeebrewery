<nav class="sticky top-0 z-40 bg-coffee-espresso text-coffee-latte shadow-[0_1px_0_rgba(253,251,247,0.14)]"
     x-data="{ mobileOpen: false }">
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 min-h-[72px] flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3 font-display font-bold text-2xl no-underline text-coffee-latte">
            <span class="grid place-items-center h-10 w-10 rounded-xl bg-coffee-amber text-coffee-espresso shrink-0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8z"/>
                    <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17"/>
                    <path d="M8 3v2M12 3v2"/>
                </svg>
            </span>
            Coffeebrewery
        </a>

        <button type="button" @click="mobileOpen = !mobileOpen"
                class="menu-btn hidden max-md:inline-flex h-11 w-11 items-center justify-center rounded-[10px] text-coffee-latte"
                aria-label="Toggle navigation menu" aria-expanded="false" :aria-expanded="mobileOpen.toString()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path :d="mobileOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 7h16M4 12h16M4 17h16'" />
            </svg>
        </button>

        <div class="hidden md:flex items-center gap-1">
            <a href="{{ route('home') }}"
               class="min-h-[44px] px-4 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors {{ request()->routeIs('home') ? 'text-coffee-latte shadow-[inset_0_-3px_0_#C87D55]' : '' }}">
                Home
            </a>
            <a href="{{ route('recipes.index') }}"
               class="min-h-[44px] px-4 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors {{ request()->routeIs('recipes.*') ? 'text-coffee-latte shadow-[inset_0_-3px_0_#C87D55]' : '' }}">
                Recipes
            </a>
            <a href="{{ route('cheap.index') }}"
               class="min-h-[44px] px-4 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors {{ request()->routeIs('cheap.*') ? 'text-coffee-latte shadow-[inset_0_-3px_0_#C87D55]' : '' }}">
                Cheap
            </a>
            <a href="{{ route('custom.index') }}"
               class="min-h-[44px] px-4 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors {{ request()->routeIs('custom.*') ? 'text-coffee-latte shadow-[inset_0_-3px_0_#C87D55]' : '' }}">
                Custom
            </a>
        </div>
    </div>

    {{-- Mobile dropdown --}}
    <div class="md:hidden border-t border-coffee-latte/10 bg-coffee-espresso px-5 pb-5 pt-1 flex flex-col"
         x-show="mobileOpen" x-cloak>
        <a href="{{ route('home') }}" class="min-h-[44px] px-2 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors">Home</a>
        <a href="{{ route('recipes.index') }}" class="min-h-[44px] px-2 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors">Recipes</a>
        <a href="{{ route('cheap.index') }}" class="min-h-[44px] px-2 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors">Cheap</a>
        <a href="{{ route('custom.index') }}" class="min-h-[44px] px-2 inline-flex items-center font-medium text-coffee-cream hover:text-coffee-amber transition-colors">Custom</a>
    </div>
</nav>
