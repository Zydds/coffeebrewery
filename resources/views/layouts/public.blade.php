<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Coffeebrewery'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600|outfit:600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.APP_I18N = @json([
            'recipeLoadError' => __('Could not load a recipe. Try again.'),
            'matchError' => __('Could not find a match. Try again.'),
        ]);

        if ('IntersectionObserver' in window) {
            document.documentElement.classList.add('js-motion');
        }
    </script>
</head>
<body class="font-sans antialiased bg-coffee-latte text-coffee-espresso">
    <x-navbar />

    <main>
        @yield('content')
    </main>

    <footer class="bg-coffee-espresso text-coffee-cream">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Zaidan Ghiffari | Coffeebrewery</p>
        </div>
    </footer>
</body>
</html>
