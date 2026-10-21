@extends('layouts.public')

@section('title', 'Brew Methods — CoffeeHub')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-coffee-espresso">Brew Methods</h1>
        <p class="mt-2 text-sm text-coffee-mocha">From pour over to espresso — pick your brewing style.</p>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($methods as $method)
                <a href="{{ route('methods.show', $method->slug) }}"
                   class="group flex flex-col rounded-lg border border-coffee-mocha/30 bg-coffee-cream overflow-hidden hover:border-coffee-amber transition-colors">
                    @if ($method->image_url)
                        <img src="{{ $method->image_url }}" alt="{{ $method->name }}"
                             class="w-full h-40 object-cover" loading="lazy">
                    @endif
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-semibold text-coffee-espresso group-hover:text-coffee-amber transition-colors">
                                {{ $method->name }}
                            </h2>
                            <span class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-full capitalize
                                {{ $method->difficulty_level === 'easy' ? 'bg-green-100 text-green-800'
                                    : ($method->difficulty_level === 'medium' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ $method->difficulty_level }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-coffee-mocha line-clamp-3 flex-1">{{ $method->description }}</p>
                        <span class="mt-3 text-xs font-semibold text-coffee-amber">
                            {{ $method->recipes_count ?? $method->recipes()->count() }} recipes &rarr;
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-coffee-mocha/40 p-10 text-center">
                    <p class="text-sm text-coffee-mocha">No brew methods yet.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
