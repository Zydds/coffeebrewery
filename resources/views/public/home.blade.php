@extends('layouts.public')

@section('title', 'CoffeeHub — Discover the Art of Coffee')

@section('content')
    {{-- Hero --}}
    <section class="bg-coffee-espresso text-coffee-latte">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 text-center">
            <h1 class="text-3xl sm:text-5xl font-bold tracking-tight leading-tight">
                Discover the Art of Coffee<br class="hidden sm:block"> &amp; Flavor Profiles
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-coffee-latte/80">
                Explore single-origin beans, master brew methods, and find your next favorite recipe.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('beans.index') }}"
                   class="px-6 py-3 rounded bg-coffee-amber text-coffee-espresso font-semibold hover:bg-coffee-amber/90 transition-colors">
                    Explore Beans
                </a>
                <button type="button"
                        @click="window.dispatchEvent(new CustomEvent('open-surprise'))"
                        class="px-6 py-3 rounded border border-coffee-latte/40 text-coffee-latte font-semibold hover:bg-white/10 transition-colors">
                    &#127922; Surprise Me Recipe
                </button>
            </div>
        </div>
    </section>

    {{-- Bean catalog preview --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-coffee-espresso">Bean Catalog</h2>
                <p class="mt-1 text-sm text-coffee-mocha">Filter by category and tap a card for flavor details.</p>
            </div>
            <a href="{{ route('beans.index') }}" class="text-sm font-semibold text-coffee-amber hover:underline">
                View all beans &rarr;
            </a>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <a href="{{ route('beans.index') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium border border-coffee-mocha/40 text-coffee-espresso hover:bg-coffee-cream transition-colors">
                All
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('beans.index') }}?category={{ $category->slug }}"
                   class="px-4 py-1.5 rounded-full text-sm font-medium border border-coffee-mocha/40 text-coffee-espresso hover:bg-coffee-cream transition-colors">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($featuredBeans as $bean)
                <x-bean-card :bean="$bean" />
            @empty
                <p class="text-sm text-coffee-mocha col-span-full">No beans in the catalog yet.</p>
            @endforelse
        </div>
    </section>

    {{-- Brew methods --}}
    <section class="bg-coffee-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-coffee-espresso">Brew Methods</h2>
                    <p class="mt-1 text-sm text-coffee-mocha">Find the brewing style that fits your routine.</p>
                </div>
                <a href="{{ route('methods.index') }}" class="text-sm font-semibold text-coffee-amber hover:underline">
                    View all methods &rarr;
                </a>
            </div>

            <div class="mt-6 grid gap-5 grid-cols-2 lg:grid-cols-4">
                @forelse ($methods as $method)
                    <a href="{{ route('methods.show', $method->slug) }}"
                       class="group block rounded-lg border border-coffee-mocha/30 bg-white p-5 hover:border-coffee-amber transition-colors">
                        @if ($method->image_url)
                            <img src="{{ $method->image_url }}" alt="{{ $method->name }}"
                                 class="w-full h-28 object-cover rounded mb-3" loading="lazy">
                        @endif
                        <h3 class="font-semibold text-coffee-espresso group-hover:text-coffee-amber transition-colors">
                            {{ $method->name }}
                        </h3>
                        <span class="mt-2 inline-block text-xs px-2 py-0.5 rounded-full bg-coffee-mocha/15 text-coffee-mocha capitalize">
                            {{ $method->difficulty_level }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-coffee-mocha col-span-full">No brew methods yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Surprise call to action --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 text-center">
        <h2 class="text-2xl font-bold text-coffee-espresso">Can't decide what to brew?</h2>
        <p class="mt-2 text-sm text-coffee-mocha">Let the random recipe generator pick your next cup.</p>
        <button type="button"
                @click="window.dispatchEvent(new CustomEvent('open-surprise'))"
                class="mt-5 px-6 py-3 rounded bg-coffee-amber text-coffee-espresso font-semibold hover:bg-coffee-amber/90 transition-colors">
            &#127922; Surprise Me Recipe
        </button>
    </section>
@endsection
