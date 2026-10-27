@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Dashboard Overview</h1>
            <p class="mt-1 text-sm text-coffee-mocha">Content across the catalog, the Cheap page and recipes.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.beans.create') }}"
               class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                + Add Bean
            </a>
            <a href="{{ route('admin.recipes.create') }}"
               class="px-4 py-2 rounded-sm bg-coffee-espresso text-coffee-latte text-sm font-semibold hover:bg-coffee-roast transition-colors">
                + Add Recipe
            </a>
            <a href="{{ route('admin.methods.create') }}"
               class="px-4 py-2 rounded-sm border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                + Add Method
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="mt-6 grid gap-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Beans</p>
            <p class="mt-1 text-3xl font-bold text-coffee-espresso">{{ $counts['beans'] }}</p>
            <p class="mt-1 text-xs text-coffee-mocha">{{ $counts['beans_regular'] }} regular &middot; {{ $counts['beans_cheap'] }} sachet/instant</p>
        </div>
        <div class="rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Recipes</p>
            <p class="mt-1 text-3xl font-bold text-coffee-espresso">{{ $counts['recipes'] }}</p>
            <p class="mt-1 text-xs text-coffee-mocha">{{ $counts['recipes_featured'] }} featured &middot; {{ $counts['recipes_cheap'] }} cheap</p>
        </div>
        <div class="rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Brew Methods</p>
            <p class="mt-1 text-3xl font-bold text-coffee-espresso">{{ $counts['methods'] }}</p>
            <p class="mt-1 text-xs text-coffee-mocha">{{ $counts['methods_regular'] }} regular &middot; {{ $counts['methods_cheap'] }} cheap</p>
        </div>
        <div class="rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Categories</p>
            <p class="mt-1 text-3xl font-bold text-coffee-espresso">{{ $counts['categories'] }}</p>
            <p class="mt-1 text-xs text-coffee-mocha">Bean classifications</p>
        </div>
    </div>

    {{-- Cheap page summary --}}
    <div class="mt-6 rounded-lg border border-coffee-mocha/30 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-coffee-espresso">Cheap page</h2>
                <p class="mt-1 text-sm text-coffee-mocha">
                    {{ $counts['beans_cheap'] }} products &middot; {{ $counts['methods_cheap'] }} brew methods &middot; {{ $counts['recipes_cheap'] }} cheap recipes
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.beans.index') }}?filter=cheap" class="text-sm font-semibold text-coffee-amber hover:underline">Manage products</a>
                <a href="{{ route('admin.recipes.index') }}" class="text-sm font-semibold text-coffee-amber hover:underline">Recipes</a>
                <a href="{{ route('cheap.index') }}" target="_blank" rel="noopener" class="text-sm font-semibold text-coffee-amber hover:underline">View page &nearr;</a>
            </div>
        </div>
    </div>

    {{-- Recent recipes --}}
    <div class="mt-8">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Recent Recipes</h2>
            <a href="{{ route('admin.recipes.index') }}" class="text-sm font-semibold text-coffee-amber hover:underline">
                View all &rarr;
            </a>
        </div>

        <div class="mt-3 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Title</th>
                        <th class="px-4 py-3 font-semibold">Pairing</th>
                        <th class="px-4 py-3 font-semibold">Tags</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-mocha/15">
                    @forelse ($recentRecipes as $recipe)
                        <tr class="hover:bg-coffee-cream/60">
                            <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $recipe->title }}</td>
                            <td class="px-4 py-3 text-coffee-mocha">
                                {{ $recipe->coffeeBean->name ?? '—' }} &middot; {{ $recipe->brewMethod->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($recipe->cheap)
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-coffee-amber text-coffee-espresso">Cheap</span>
                                    @endif
                                    @if ($recipe->is_featured)
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-coffee-espresso text-coffee-latte">Featured</span>
                                    @endif
                                    @if (!$recipe->cheap && !$recipe->is_featured)
                                        <span class="text-xs text-coffee-mocha">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.recipes.edit', $recipe) }}" class="font-semibold text-coffee-amber hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-coffee-mocha">No recipes yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent beans --}}
    <div class="mt-8">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Recent Bean Additions</h2>
            <a href="{{ route('admin.beans.index') }}" class="text-sm font-semibold text-coffee-amber hover:underline">
                View all &rarr;
            </a>
        </div>

        <div class="mt-3 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Name</th>
                        <th class="px-4 py-3 font-semibold">Category</th>
                        <th class="px-4 py-3 font-semibold">Origin</th>
                        <th class="px-4 py-3 font-semibold">Price</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-mocha/15">
                    @forelse ($recentBeans as $bean)
                        <tr class="hover:bg-coffee-cream/60">
                            <td class="px-4 py-3 font-medium text-coffee-espresso">
                                {{ $bean->name }}
                                @if ($bean->is_cheap)
                                    <span class="ml-1.5 text-xs font-semibold px-2 py-0.5 rounded-full bg-coffee-amber text-coffee-espresso">Cheap</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-coffee-mocha">{{ $bean->category->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-coffee-mocha">{{ $bean->origin_country }}</td>
                            <td class="px-4 py-3 text-coffee-mocha">
                                @if ($bean->price !== null)
                                    Rp. {{ number_format((float) $bean->price, 0, ',', '.') }} / {{ $bean->price_unit ?: '100 gr' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.beans.edit', $bean) }}" class="font-semibold text-coffee-amber hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-coffee-mocha">No beans yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
