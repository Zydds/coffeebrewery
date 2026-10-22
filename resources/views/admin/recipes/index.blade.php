@extends('admin.layout')

@section('title', 'Recipes')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Recipes</h1>
            <p class="mt-1 text-sm text-coffee-mocha">Brew guides across beans and methods.</p>
        </div>
        <a href="{{ route('admin.recipes.create') }}"
           class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
            + Add Recipe
        </a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                <tr>
                    <th class="px-4 py-3 font-semibold">Title</th>
                    <th class="px-4 py-3 font-semibold">Bean</th>
                    <th class="px-4 py-3 font-semibold">Method</th>
                    <th class="px-4 py-3 font-semibold">Time</th>
                    <th class="px-4 py-3 font-semibold">Featured</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-mocha/15">
                @forelse ($recipes as $recipe)
                    <tr class="hover:bg-coffee-cream/60">
                        <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $recipe->title }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $recipe->coffeeBean->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $recipe->brewMethod->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $recipe->brew_time_min ? $recipe->brew_time_min.' min' : '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($recipe->is_featured)
                                <span class="rounded-full bg-coffee-amber px-2 py-0.5 text-xs font-medium text-coffee-espresso">Yes</span>
                            @else
                                <span class="text-sm text-coffee-mocha">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.recipes.edit', $recipe) }}" class="font-semibold text-coffee-amber hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.recipes.destroy', $recipe) }}" class="inline ml-3"
                                  onsubmit="return confirm('Delete this recipe?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-coffee-mocha">No recipes yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
