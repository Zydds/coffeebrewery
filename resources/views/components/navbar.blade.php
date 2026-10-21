<nav class="sticky top-0 z-40 bg-coffee-espresso text-coffee-latte shadow" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg tracking-tight">
                <span aria-hidden="true">&#9749;</span>
                <span>CoffeeHub</span>
            </a>

            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}"
                   class="hover:text-coffee-amber transition-colors {{ request()->routeIs('home') ? 'text-coffee-amber' : '' }}">
                    Home
                </a>
                <a href="{{ route('beans.index') }}"
                   class="hover:text-coffee-amber transition-colors {{ request()->routeIs('beans.*') ? 'text-coffee-amber' : '' }}">
                    Bean Catalog
                </a>
                <a href="{{ route('recipes.index') }}"
                   class="hover:text-coffee-amber transition-colors {{ request()->routeIs('recipes.*') ? 'text-coffee-amber' : '' }}">
                    Recipes
                </a>
                <a href="{{ route('methods.index') }}"
                   class="hover:text-coffee-amber transition-colors {{ request()->routeIs('methods.*') ? 'text-coffee-amber' : '' }}">
                    Brew Methods
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-1.5 rounded bg-coffee-amber text-coffee-espresso font-semibold hover:bg-coffee-amber/90 transition-colors">
                        Admin
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-3 py-1.5 rounded bg-coffee-amber text-coffee-espresso font-semibold hover:bg-coffee-amber/90 transition-colors">
                        Admin Login
                    </a>
                @endauth
            </div>

            <button type="button" @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded hover:bg-coffee-roast transition-colors"
                    aria-label="Toggle navigation menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          :d="mobileOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'" />
                </svg>
            </button>
        </div>

        <div class="md:hidden border-t border-coffee-roast py-3 space-y-1" x-show="mobileOpen" x-cloak>
            <a href="{{ route('home') }}" class="block px-2 py-2 rounded hover:bg-coffee-roast transition-colors">Home</a>
            <a href="{{ route('beans.index') }}" class="block px-2 py-2 rounded hover:bg-coffee-roast transition-colors">Bean Catalog</a>
            <a href="{{ route('recipes.index') }}" class="block px-2 py-2 rounded hover:bg-coffee-roast transition-colors">Recipes</a>
            <a href="{{ route('methods.index') }}" class="block px-2 py-2 rounded hover:bg-coffee-roast transition-colors">Brew Methods</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block px-2 py-2 rounded bg-coffee-amber text-coffee-espresso font-semibold">Admin</a>
            @else
                <a href="{{ route('login') }}" class="block px-2 py-2 rounded bg-coffee-amber text-coffee-espresso font-semibold">Admin Login</a>
            @endauth
        </div>
    </div>
</nav>
