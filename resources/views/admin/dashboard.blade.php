@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Dashboard Overview</h1>
            <p class="mt-1 text-sm text-coffee-mocha">Content counts across the catalog.</p>
        </div>
        <a href="{{ route('admin.beans.create') }}"
           class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
            + Add New Coffee Bean
        </a>
    </div>

    {{-- Counts --}}
    <div class="mt-6 grid gap-4 grid-cols-1 sm:grid-cols-3">
        <div class="rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Beans</p>
            <p class="mt-1 text-3xl font-bold text-coffee-espresso">{{ $counts['beans'] }}</p>
        </div>
        <div class="rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Recipes</p>
            <p class="mt-1 text-3xl font-bold text-coffee-espresso">{{ $counts['recipes'] }}</p>
        </div>
        <div class="rounded-lg border border-coffee-mocha/30 bg-coffee-cream p-5">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Methods</p>
            <p class="mt-1 text-3xl font-bold text-coffee-espresso">{{ $counts['methods'] }}</p>
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
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-mocha/15">
                    @forelse ($recentBeans as $bean)
                        <tr class="hover:bg-coffee-cream/60">
                            <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $bean->name }}</td>
                            <td class="px-4 py-3 text-coffee-mocha">{{ $bean->category->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-coffee-mocha">{{ $bean->origin_country }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.beans.edit', $bean) }}" class="font-semibold text-coffee-amber hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-coffee-mocha">No beans yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
