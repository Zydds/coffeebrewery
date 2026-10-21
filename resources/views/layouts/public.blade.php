<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'CoffeeHub'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-coffee-latte text-coffee-espresso">
    <x-navbar />

    <main>
        @yield('content')
    </main>

    <footer class="bg-coffee-espresso text-coffee-latte mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-coffee-latte/80">
            &copy; {{ date('Y') }} Zaidan Ghiffari | CoffeeHub
        </div>
    </footer>

    <x-surprise-modal />
</body>
</html>
