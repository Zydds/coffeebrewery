<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-clip">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Coffeebrewery'))</title>

    @php
        // Per-page SEO values, shared by the meta, Open Graph, and Twitter
        // tags below (and the recipe JSON-LD). Every public page can override
        // meta_description / og_image / og_type via sections; the description
        // fallback doubles as the home page's description.
        $pageTitle = trim($__env->yieldContent('title', config('app.name', 'Coffeebrewery')));
        $metaDescription = trim($__env->yieldContent('meta_description', __('Coffeebrewery helps you brew better at home: a bean catalog, brew method guides, budget picks, and recipes matched to your gear.')));
        $ogImage = trim($__env->yieldContent('og_image'));
    @endphp

    <meta name="description" content="{!! $metaDescription !!}">

    {{-- Locale lives in the session (one URL serves both languages), so the
         canonical is simply this URL and no hreflang alternates apply. --}}
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ config('app.name', 'Coffeebrewery') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'id' ? 'id_ID' : 'en_US' }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{!! $pageTitle !!}">
    <meta property="og:description" content="{!! $metaDescription !!}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage !== '')
        <meta property="og:image" content="{!! $ogImage !!}">
    @endif

    <meta name="twitter:card" content="{{ $ogImage !== '' ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{!! $pageTitle !!}">
    <meta name="twitter:description" content="{!! $metaDescription !!}">
    @if ($ogImage !== '')
        <meta name="twitter:image" content="{!! $ogImage !!}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600|outfit:600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.APP_LOCALE = @json(app()->getLocale());

        window.APP_I18N = @json([
            'recipeLoadError' => __('Could not load a recipe. Try again.'),
            'matchError' => __('Could not find a match. Try again.'),
        ]);

        if ('IntersectionObserver' in window) {
            document.documentElement.classList.add('js-motion');
        }

        // Recipes pagination: the click handler stashes the direction, and
        // we paint the incoming grid already 16px off before first paint so
        // the 250ms settle tween never flashes at its end position. Consume
        // the key here so a cancelled navigation cannot leak into a later page.
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            var recipesDir = sessionStorage.getItem('recipes-page-dir');
            sessionStorage.removeItem('recipes-page-dir');

            if (recipesDir === 'next' || recipesDir === 'prev') {
                document.documentElement.classList.add('page-enter-' + recipesDir);
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-coffee-latte text-coffee-espresso overflow-x-clip">
    <x-coffee-illustrations />

    <x-navbar />

    <main>
        @yield('content')
    </main>

    @yield('before_footer')

    <footer class="bg-coffee-espresso text-coffee-cream">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Zaidan Ghiffari | Coffeebrewery</p>
        </div>
    </footer>
</body>
</html>
