@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Edit Category</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Update the details of this category.</p>
    </div>

    <div class="mt-6 max-w-2xl rounded-lg border border-coffee-mocha/30 bg-white p-6">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('name', $category->name)" required maxlength="100" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="4" maxlength="500"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-xs focus:border-coffee-amber focus:ring-coffee-amber text-sm">{{ old('description', $category->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                    Save
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="px-4 py-2 rounded-sm border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
