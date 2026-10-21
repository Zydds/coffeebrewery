@extends('admin.layout')

@section('title', 'Coffee Beans')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Coffee Beans</h1>
            <p class="mt-1 text-sm text-coffee-mocha">Manage the coffee bean catalog.</p>
        </div>
        <a href="{{ route('admin.beans.create') }}"
           class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
            + Add Coffee Bean
        </a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                <tr>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Category</th>
                    <th class="px-4 py-3 font-semibold">Origin</th>
                    <th class="px-4 py-3 font-semibold">Acidity</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-mocha/15">
                @forelse ($beans as $bean)
                    <tr class="hover:bg-coffee-cream/60">
                        <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $bean->name }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $bean->category->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $bean->origin_country }}</td>
                        <td class="px-4 py-3 text-coffee-amber tracking-wide">{{ str_repeat('★', $bean->acidity_level) . str_repeat('☆', 5 - $bean->acidity_level) }}</td>
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
                        <td colspan="5" class="px-4 py-6 text-center text-coffee-mocha">No beans yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
