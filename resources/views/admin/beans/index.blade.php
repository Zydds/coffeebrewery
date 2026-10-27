@extends('admin.layout')

@section('title', 'Coffee Beans')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Coffee Beans</h1>
            <p class="mt-1 text-sm text-coffee-mocha">Manage the coffee bean catalog.</p>
        </div>
        <a href="{{ route('admin.beans.create') }}"
           class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
            + Add Coffee Bean
        </a>
    </div>

    <div class="mt-4 flex flex-wrap gap-2" role="navigation" aria-label="Filter beans">
        <a href="{{ route('admin.beans.index') }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ request('filter') === null
                ? 'bg-coffee-espresso text-coffee-latte'
                : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
            All
        </a>
        <a href="{{ route('admin.beans.index', ['filter' => 'regular']) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ request('filter') === 'regular'
                ? 'bg-coffee-espresso text-coffee-latte'
                : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
            Regular
        </a>
        <a href="{{ route('admin.beans.index', ['filter' => 'cheap']) }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ request('filter') === 'cheap'
                ? 'bg-coffee-espresso text-coffee-latte'
                : 'bg-coffee-cream text-coffee-espresso hover:bg-coffee-espresso hover:text-coffee-latte' }}">
            Cheap page
        </a>
    </div>

    <div class="mt-4 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                <tr>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Category</th>
                    <th class="px-4 py-3 font-semibold">Origin</th>
                    <th class="px-4 py-3 font-semibold">Acidity</th>
                    <th class="px-4 py-3 font-semibold">Price</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-mocha/15">
                @forelse ($beans as $bean)
                    <tr class="hover:bg-coffee-cream/60">
                        <td class="px-4 py-3 font-medium text-coffee-espresso">
                            {{ $bean->name }}
                            @if ($bean->is_cheap)
                                <span class="ml-1.5 text-xs font-semibold px-2 py-0.5 rounded-full bg-coffee-amber text-coffee-espresso">Cheap</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $bean->category->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $bean->origin_country }}</td>
                        <td class="px-4 py-3 text-coffee-amber tracking-wide">{{ str_repeat('★', $bean->acidity_level) . str_repeat('☆', 5 - $bean->acidity_level) }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">
                            @if ($bean->price !== null)
                                Rp. {{ number_format((float) $bean->price, 0, ',', '.') }} / {{ $bean->price_unit ?: '100 gr' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.beans.edit', $bean) }}" class="font-semibold text-coffee-amber hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.beans.destroy', $bean) }}" class="inline ml-3"
                                  onsubmit="return confirm('Delete this coffee bean?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-coffee-mocha">No beans found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
