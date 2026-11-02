@extends('layouts.public')

@section('title', $method->name . ' — Coffeebrewery')

@section('meta_description', __(':name explained — how it works, the equipment you need, and its difficulty level, plus recipes brewed with it.', [
    'name' => $method->name,
]))

@section('og_image', $method->image_url ?? '')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('home') }}#brew-methods" class="text-sm font-semibold text-coffee-amber-ink hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink rounded-sm">
            &larr; {{ __('All methods') }}
        </a>

        <div class="mt-4 rounded-2xl border border-coffee-mocha/30 bg-coffee-cream overflow-hidden">
            @if ($method->image_url)
                <img src="{{ $method->image_url }}" alt="{{ $method->name }}" class="w-full h-64 object-cover">
            @endif

            <div class="p-6 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <h1 class="font-display text-[clamp(2rem,4vw,3rem)] font-extrabold leading-[1.1] tracking-tight text-coffee-espresso">{{ $method->name }}</h1>
                    <span class="text-sm font-medium px-3 py-1 rounded-full
                        {{ $method->difficulty_level === 'easy' ? 'bg-green-100 text-green-800'
                            : ($method->difficulty_level === 'medium' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        {{ __(ucfirst($method->difficulty_level)) }}
                    </span>
                </div>

                <p class="mt-4 text-sm leading-relaxed text-coffee-mocha">{{ __($method->description) }}</p>

                <div class="mt-6">
                    <h2 class="eyebrow">{{ __('Equipment Needed') }}</h2>
                    <ul class="mt-2 grid gap-2 sm:grid-cols-2">
                        @forelse ($method->equipment_needed ?? [] as $item)
                            <li class="flex items-center gap-2 text-sm text-coffee-espresso bg-white border border-coffee-mocha/20 rounded-xl px-3 py-2">
                                <span class="text-coffee-amber-ink" aria-hidden="true">✓</span>
                                {{ $item }}
                            </li>
                        @empty
                            <li class="text-sm text-coffee-mocha/70">{{ __('No equipment listed.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('recipes.index') }}"
               class="min-h-[44px] px-6 inline-flex items-center rounded-xl bg-coffee-espresso text-sm font-semibold text-coffee-latte transition-colors duration-150 ease-out hover:bg-coffee-amber hover:text-coffee-espresso motion-safe:active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">
                {{ __('Browse Recipes') }}
            </a>
        </div>
    </div>
@endsection
