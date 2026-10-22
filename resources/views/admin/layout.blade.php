<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Coffee CMS') — {{ config('app.name', 'Coffeebrewery') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600|outfit:600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-coffee-latte text-coffee-espresso">
    <div class="min-h-screen flex flex-col md:flex-row">
        {{-- Sidebar --}}
        <aside class="md:w-64 shrink-0 bg-coffee-espresso text-coffee-latte md:min-h-screen"
               x-data="{ mobileOpen: false }">
            <div class="flex items-center justify-between px-5 py-4 border-b border-coffee-roast">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg tracking-tight">
                    Coffee CMS
                </a>
                <button type="button" class="md:hidden p-1" @click="mobileOpen = !mobileOpen" aria-label="Toggle sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <nav class="px-3 py-4 space-y-1" :class="mobileOpen ? 'block' : 'hidden md:block'">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-coffee-amber text-coffee-espresso' : 'hover:bg-coffee-roast' }}">
                    Overview
                </a>
                <a href="{{ route('admin.beans.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('admin.beans.*') ? 'bg-coffee-amber text-coffee-espresso' : 'hover:bg-coffee-roast' }}">
                    Beans
                </a>
                <a href="{{ route('admin.recipes.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('admin.recipes.*') ? 'bg-coffee-amber text-coffee-espresso' : 'hover:bg-coffee-roast' }}">
                    Recipes
                </a>
                <a href="{{ route('admin.methods.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('admin.methods.*') ? 'bg-coffee-amber text-coffee-espresso' : 'hover:bg-coffee-roast' }}">
                    Methods
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-coffee-amber text-coffee-espresso' : 'hover:bg-coffee-roast' }}">
                    Categories
                </a>

                <div class="pt-4 mt-4 border-t border-coffee-roast">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded text-sm font-medium text-coffee-latte/70 hover:bg-coffee-roast transition-colors">
                        View Site
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit"
                                class="w-full text-left flex items-center gap-2 px-3 py-2 rounded text-sm font-medium text-coffee-latte/70 hover:bg-coffee-roast transition-colors">
                            ← Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if (session('success'))
                    <div class="mb-6 rounded border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800"
                         role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800"
                         role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
