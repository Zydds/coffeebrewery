@extends('admin.layout')

@section('title', 'Add Coffee Bean')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Add Coffee Bean</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Add a new bean to the catalog.</p>
    </div>

    <div class="mt-6 max-w-2xl rounded-lg border border-coffee-mocha/30 bg-white p-6">
        <form method="POST" action="{{ route('admin.beans.store') }}" enctype="multipart/form-data">
            @csrf

            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('name')" required maxlength="150" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="origin_country" value="Origin Country" />
                <x-text-input id="origin_country" name="origin_country" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('origin_country')" required maxlength="100" />
                <x-input-error :messages="$errors->get('origin_country')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="acidity_level" value="Acidity Level" />
                <select id="acidity_level" name="acidity_level" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" @selected(old('acidity_level') == $i)>{{ $i }} ({{ $i === 1 ? 'low' : ($i === 5 ? 'high' : 'medium') }})</option>
                    @endfor
                </select>
                <x-input-error :messages="$errors->get('acidity_level')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="body_level" value="Body Level" />
                <select id="body_level" name="body_level" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" @selected(old('body_level') == $i)>{{ $i }} ({{ $i === 1 ? 'low' : ($i === 5 ? 'high' : 'medium') }})</option>
                    @endfor
                </select>
                <x-input-error :messages="$errors->get('body_level')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="flavor_notes" value="Flavor Notes" />
                <div x-data="{ items: {{ json_encode(old('flavor_notes', [''])) }} }">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 mt-2">
                            <input type="text" :name="'flavor_notes[' + index + ']'" x-model="items[index]" maxlength="40"
                                   class="flex-1 rounded border-gray-300 text-sm focus:border-coffee-amber focus:ring-coffee-amber"
                                   placeholder="e.g. citrus, chocolate">
                            <button type="button" @click="items.splice(index, 1)" class="text-sm text-red-600 hover:underline">Remove</button>
                        </div>
                    </template>
                    <button type="button" @click="items.push('')" class="mt-2 text-sm font-semibold text-coffee-amber hover:underline">+ Add item</button>
                </div>
                <x-input-error :messages="$errors->get('flavor_notes')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="recommended_drinks" value="Recommended Drinks" />
                <div x-data="{ items: {{ json_encode(old('recommended_drinks', [''])) }} }">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 mt-2">
                            <input type="text" :name="'recommended_drinks[' + index + ']'" x-model="items[index]" maxlength="60"
                                   class="flex-1 rounded border-gray-300 text-sm focus:border-coffee-amber focus:ring-coffee-amber"
                                   placeholder="e.g. iced latte, espresso">
                            <button type="button" @click="items.splice(index, 1)" class="text-sm text-red-600 hover:underline">Remove</button>
                        </div>
                    </template>
                    <button type="button" @click="items.push('')" class="mt-2 text-sm font-semibold text-coffee-amber hover:underline">+ Add item</button>
                </div>
                <x-input-error :messages="$errors->get('recommended_drinks')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="image" value="Image" />
                <input id="image" name="image" type="file"
                       accept="image/jpg,image/jpeg,image/png,image/webp"
                       class="mt-1 block w-full text-sm text-coffee-mocha" />
                <p class="mt-1 text-xs text-coffee-mocha">JPG, PNG or WebP — max 4MB</p>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                    Save
                </button>
                <a href="{{ route('admin.beans.index') }}"
                   class="px-4 py-2 rounded border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
