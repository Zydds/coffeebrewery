@extends('layouts.public')

@section('title', $bean->name . ' — Coffeebrewery')

@section('meta_description', __(':name beans from :origin — flavor notes, acidity and body, price, and the recipes that use them.', [
    'name' => $bean->name,
    'origin' => $bean->origin_country ?: __('Unknown'),
]))

@section('og_image', $bean->image_url ?? '')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('home') }}#bean-catalog" class="text-sm font-semibold text-coffee-amber hover:underline">
            &larr; {{ __('All beans') }}
        </a>

        <div class="mt-4 rounded-lg border border-coffee-mocha/30 bg-coffee-cream overflow-hidden">
            @if ($bean->image_url)
                <img src="{{ $bean->image_url }}" alt="{{ $bean->name }}" class="w-full h-64 object-cover">
            @endif

            <div class="p-6 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <h1 class="text-3xl font-bold text-coffee-espresso">{{ $bean->name }}</h1>
                    <span class="text-sm font-medium px-3 py-1 rounded-full bg-coffee-mocha/15 text-coffee-mocha">
                        {{ $bean->category->name ?? __('Uncategorized') }}
                    </span>
                </div>

                <dl class="mt-6 grid gap-5 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-coffee-mocha">{{ __('Origin') }}</dt>
                        <dd class="mt-1 font-semibold text-coffee-espresso text-base">{{ $bean->origin_country }}</dd>
                    </div>

                    <div>
                        <dt class="text-coffee-mocha">{{ __('Acidity') }}</dt>
                        <dd class="mt-1 flex items-center gap-2" role="img" aria-label="{{ __('Acidity :level of 5', ['level' => $bean->acidity_level]) }}">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="h-3 w-3 rounded-full border-2 border-coffee-mocha {{ $i <= $bean->acidity_level ? 'border-coffee-espresso bg-coffee-espresso' : '' }}"></i>
                            @endfor
                        </dd>
                    </div>

                    <div>
                        <dt class="text-coffee-mocha">{{ __('Body') }}</dt>
                        <dd class="mt-1 flex items-center gap-2" role="img" aria-label="{{ __('Body :level of 5', ['level' => $bean->body_level]) }}">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="h-3 w-3 rounded-full border-2 border-coffee-mocha {{ $i <= $bean->body_level ? 'border-coffee-espresso bg-coffee-espresso' : '' }}"></i>
                            @endfor
                        </dd>
                    </div>

                    <div>
                        <dt class="text-coffee-mocha">{{ __('Price') }}</dt>
                        <dd class="mt-1 font-semibold text-coffee-espresso text-base">
                            @if ($bean->price !== null)
                                Rp. {{ number_format((float) $bean->price, 0, ',', '.') }} / {{ $bean->price_unit ?: '100 gr' }}
                            @else
                                &mdash;
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-coffee-mocha">{{ __('Recommended Drinks') }}</dt>
                        <dd class="mt-1 font-medium text-coffee-espresso">
                            @forelse ($bean->recommended_drinks ?? [] as $drink)
                                <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-white border border-coffee-mocha/30 mr-1.5 mb-1">{{ $drink }}</span>
                            @empty
                                <span class="text-coffee-mocha/70">{{ __('None listed') }}</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <h2 class="text-sm font-semibold text-coffee-mocha uppercase tracking-wider">{{ __('Flavor Notes') }}</h2>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @forelse ($bean->flavor_notes ?? [] as $note)
                            <span class="text-sm px-3 py-1 rounded-full bg-coffee-amber/20 text-coffee-espresso border border-coffee-amber/40">{{ $note }}</span>
                        @empty
                            <span class="text-sm text-coffee-mocha/70">{{ __('No flavor notes listed.') }}</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('recipes.index') }}"
               class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                {{ __('Browse Recipes') }}
            </a>
        </div>
    </div>
@endsection
