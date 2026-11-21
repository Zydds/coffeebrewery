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
    <div class="min-h-screen flex flex-col lg:flex-row">
        {{-- Sidebar --}}
        <aside class="lg:w-72 shrink-0 bg-coffee-espresso text-coffee-latte lg:min-h-screen"
               x-data="{ mobileOpen: false }">
            <div class="flex items-center justify-between px-5 py-5 lg:sticky lg:top-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 font-display text-xl font-bold tracking-tight">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-coffee-amber text-coffee-espresso" aria-hidden="true">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8z"/><path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M8 3v2M12 3v2"/></svg>
                    </span>
                    Coffee CMS
                </a>
                <button type="button" class="grid h-10 w-10 place-items-center rounded-xl text-coffee-latte transition-colors hover:bg-coffee-roast lg:hidden" @click="mobileOpen = !mobileOpen" aria-label="{{ __('Toggle sidebar') }}" :aria-expanded="mobileOpen.toString()">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path :d="mobileOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 7h16M4 12h16M4 17h16'" /></svg>
                </button>
            </div>

            <nav class="px-4 pb-5 lg:sticky lg:top-[88px]" :class="mobileOpen ? 'block' : 'hidden lg:block'" aria-label="{{ __('Admin navigation') }}">
                <p class="px-3 pb-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-coffee-latte/45">{{ __('Workspace') }}</p>
                <div class="space-y-1">
                    @foreach ([
                        ['route' => 'dashboard', 'label' => 'Overview', 'active' => request()->routeIs('dashboard')],
                        ['route' => 'admin.beans.index', 'label' => 'Beans', 'active' => request()->routeIs('admin.beans.*')],
                        ['route' => 'admin.recipes.index', 'label' => 'Recipes', 'active' => request()->routeIs('admin.recipes.*')],
                        ['route' => 'admin.methods.index', 'label' => 'Methods', 'active' => request()->routeIs('admin.methods.*')],
                        ['route' => 'admin.categories.index', 'label' => 'Categories', 'active' => request()->routeIs('admin.categories.*')],
                        ['route' => 'admin.cafes.index', 'label' => 'Cafes', 'active' => request()->routeIs('admin.cafes.*')],
                        ['route' => 'admin.import.create', 'label' => 'Import', 'active' => request()->routeIs('admin.import.*')],
                    ] as $item)
                        <a href="{{ route($item['route']) }}" @class([
                            'group flex min-h-11 items-center justify-between rounded-xl px-3 text-sm font-medium transition-[background-color,color,transform] duration-150 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber',
                            'bg-coffee-amber text-coffee-espresso' => $item['active'],
                            'text-coffee-latte/75 hover:bg-coffee-roast hover:text-coffee-latte hover:translate-x-0.5' => ! $item['active'],
                        ])>
                            <span>{{ __($item['label']) }}</span>
                            @if ($item['active']) <span class="h-1.5 w-1.5 rounded-full bg-coffee-espresso" aria-hidden="true"></span> @endif
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 border-t border-coffee-roast pt-5">
                    <p class="px-3 pb-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-coffee-latte/45">{{ __('Shortcuts') }}</p>
                    <a href="{{ route('home') }}" class="flex min-h-11 items-center rounded-xl px-3 text-sm font-medium text-coffee-latte/70 transition-[background-color,color] duration-150 hover:bg-coffee-roast hover:text-coffee-latte focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber">{{ __('View site') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="flex min-h-11 w-full items-center rounded-xl px-3 text-left text-sm font-medium text-coffee-latte/70 transition-[background-color,color] duration-150 hover:bg-coffee-roast hover:text-coffee-latte focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber">{{ __('Log out') }}</button>
                    </form>
                </div>
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if (session('success'))
                    <div class="mb-6 rounded-sm border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800"
                         role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-sm border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800"
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
