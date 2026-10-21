@extends('admin.layout')

@section('title', 'Brew Methods')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Brew Methods</h1>
            <p class="mt-1 text-sm text-coffee-mocha">Manage brewing methods and their difficulty.</p>
        </div>
        <a href="{{ route('admin.methods.create') }}"
           class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
            + Add Brew Method
        </a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                <tr>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Difficulty</th>
                    <th class="px-4 py-3 font-semibold">Recipes</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-mocha/15">
                @forelse ($methods as $method)
                    <tr class="hover:bg-coffee-cream/60">
                        <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $method->name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $method->difficulty_level === 'easy' ? 'bg-green-100 text-green-800' : ($method->difficulty_level === 'medium' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($method->difficulty_level) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $method->recipes_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.methods.edit', $method) }}" class="font-semibold text-coffee-amber hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.methods.destroy', $method) }}" class="inline ml-3"
                                  onsubmit="return confirm('Delete this brew method?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-coffee-mocha">No brew methods yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
