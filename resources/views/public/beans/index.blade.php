@extends('layouts.public')

@section('title', 'Bean Catalog — CoffeeHub')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-coffee-espresso">Bean Catalog</h1>
        <p class="mt-2 text-sm text-coffee-mocha">Filter by category. Hover a card on desktop, tap on mobile.</p>

        {{-- Category filter --}}
        <div class="mt-6 flex flex-wrap gap-2" role="navigation" aria-label="Category filter">
            <a href="{{ route('beans.index') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ request('category') === null
                    ? 'bg-coffee-espresso text-coffee-latte border-coffee-espresso'
                    : 'border-coffee-mocha/40 text-coffee-espresso hover:bg-coffee-cream' }}">
                All
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('beans.index') }}?category={{ $category->slug }}"
                   class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ request('category') === $category->slug
                        ? 'bg-coffee-espresso text-coffee-latte border-coffee-espresso'
                        : 'border-coffee-mocha/40 text-coffee-espresso hover:bg-coffee-cream' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        {{-- Beans --}}
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($beans as $bean)
                <x-bean-card :bean="$bean" />
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-coffee-mocha/40 p-10 text-center">
                    <p class="text-sm text-coffee-mocha">
                        No beans found{{ request('category') ? ' in this category' : '' }}.
                    </p>
                    @if (request('category'))
                        <a href="{{ route('beans.index') }}" class="mt-3 inline-block text-sm font-semibold text-coffee-amber hover:underline">
                            Clear filter
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
@endsection
