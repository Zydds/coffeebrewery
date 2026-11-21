<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <title>{{ config('app.name', 'Coffeebrewery') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-coffee-latte font-sans text-coffee-espresso antialiased">
        <main class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-2 bg-coffee-amber" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-coffee-cream" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-coffee-cream" aria-hidden="true"></div>

            <div class="relative mx-auto grid min-h-screen max-w-[1200px] items-center gap-12 px-5 py-12 sm:px-8 lg:grid-cols-[.9fr_1.1fr] lg:gap-20 lg:py-16">
                <section class="hidden lg:block" aria-labelledby="login-intro">
                    <a href="{{ route('home') }}" class="mb-12 inline-flex items-center gap-3 font-display text-2xl font-bold text-coffee-espresso">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-coffee-amber text-coffee-espresso" aria-hidden="true">
                            <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8z"/><path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M8 3v2M12 3v2"/>
                            </svg>
                        </span>
                        Coffeebrewery
                    </a>
                    <p class="eyebrow">{{ __('Admin workspace') }}</p>
                    <h1 id="login-intro" class="mt-4 max-w-md font-display text-5xl font-extrabold tracking-tight text-coffee-espresso xl:text-6xl">{{ __('Make every brew count.') }}</h1>
                    <p class="mt-6 max-w-md text-lg leading-8 text-coffee-mocha">{{ __('Manage beans, recipes, methods, and cafes from one quiet corner of the roastery.') }}</p>
                </section>

                <section class="mx-auto w-full max-w-md" aria-labelledby="login-title">
                    <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-3 font-display text-xl font-bold text-coffee-espresso lg:hidden">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-coffee-amber text-coffee-espresso" aria-hidden="true">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8z"/><path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M8 3v2M12 3v2"/></svg>
                        </span>
                        Coffeebrewery
                    </a>
                    <div class="rounded-3xl border border-coffee-mocha/20 bg-coffee-cream p-7 shadow-[0_18px_50px_rgba(44,26,20,0.10)] sm:p-9">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
