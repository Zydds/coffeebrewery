@extends('admin.layout')

@section('title', 'Categories')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Categories</h1>
            <p class="mt-1 text-sm text-coffee-mocha">Organize coffee beans into categories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
            + Add Category
        </a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                <tr>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Slug</th>
                    <th class="px-4 py-3 font-semibold">Description</th>
                    <th class="px-4 py-3 font-semibold">Beans</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-mocha/15">
                @forelse ($categories as $category)
                    <tr class="hover:bg-coffee-cream/60">
                        <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $category->slug }}</td>
                        <td class="px-4 py-3 text-coffee-mocha">
                            <span class="line-clamp-1">{{ $category->description ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $category->coffeeBeans_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="font-semibold text-coffee-amber hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline ml-3"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-coffee-mocha">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
