@extends('admin.layout')

@section('title', 'Edit Coffee Bean')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Edit Coffee Bean</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Update this bean's details.</p>
    </div>

    @if ($bean->image_url)
        <div class="mt-4 flex items-center gap-4">
            <img src="{{ $bean->image_url }}" alt="{{ $bean->name }}" class="w-32 h-32 object-cover rounded-sm border border-coffee-mocha/30">
            <p class="text-xs text-coffee-mocha">Uploading a new image replaces the current one.</p>
        </div>
    @endif

    <div class="mt-6 max-w-2xl rounded-lg border border-coffee-mocha/30 bg-white p-6">
        <form method="POST" action="{{ route('admin.beans.update', $bean) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-xs focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $bean->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('name', $bean->name)" required maxlength="150" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="origin_country" value="Origin Country" />
                <x-text-input id="origin_country" name="origin_country" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('origin_country', $bean->origin_country)" required maxlength="100" />
                <x-input-error :messages="$errors->get('origin_country')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="acidity_level" value="Acidity Level" />
                <select id="acidity_level" name="acidity_level" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-xs focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" @selected(old('acidity_level', $bean->acidity_level) == $i)>{{ $i }} ({{ $i === 1 ? 'low' : ($i === 5 ? 'high' : 'medium') }})</option>
                    @endfor
                </select>
                <x-input-error :messages="$errors->get('acidity_level')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="body_level" value="Body Level" />
                <select id="body_level" name="body_level" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-xs focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" @selected(old('body_level', $bean->body_level) == $i)>{{ $i }} ({{ $i === 1 ? 'low' : ($i === 5 ? 'high' : 'medium') }})</option>
                    @endfor
                </select>
                <x-input-error :messages="$errors->get('body_level')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="price" value="Price (Rp)" />
                <x-text-input id="price" name="price" type="number" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('price', $bean->price)" min="0" step="1000" placeholder="e.g. 85000" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="price_unit" value="Price unit" />
                <x-text-input id="price_unit" name="price_unit" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('price_unit', $bean->price_unit ?: '100 gr')" maxlength="30" placeholder="e.g. 100 gr, sachet" />
                <p class="mt-1 text-xs text-coffee-mocha">Shown as: Rp. 70.000 / 100 gr</p>
                <x-input-error :messages="$errors->get('price_unit')" class="mt-2" />
            </div>

            <div class="mt-4">
                <input type="hidden" name="is_cheap" value="0">
                <label for="is_cheap" class="inline-flex items-center gap-2">
                    <input id="is_cheap" name="is_cheap" type="checkbox" value="1"
                           @checked(old('is_cheap', $bean->is_cheap))
                           class="rounded-sm border-gray-300 text-coffee-amber focus:ring-coffee-amber">
                    <span class="text-sm font-medium text-gray-700">Show on the Cheap page (sachet / instant product)</span>
                </label>
                <x-input-error :messages="$errors->get('is_cheap')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="flavor_notes" value="Flavor Notes" />
                <div x-data="{ items: {{ json_encode(old('flavor_notes', $bean->flavor_notes ?? [''])) }} }">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 mt-2">
                            <input type="text" :name="'flavor_notes[' + index + ']'" x-model="items[index]" maxlength="40"
                                   class="flex-1 rounded-sm border-gray-300 text-sm focus:border-coffee-amber focus:ring-coffee-amber"
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
                <div x-data="{ items: {{ json_encode(old('recommended_drinks', $bean->recommended_drinks ?? [''])) }} }">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 mt-2">
                            <input type="text" :name="'recommended_drinks[' + index + ']'" x-model="items[index]" maxlength="60"
                                   class="flex-1 rounded-sm border-gray-300 text-sm focus:border-coffee-amber focus:ring-coffee-amber"
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
                <p class="mt-1 text-xs text-coffee-mocha">JPG, PNG or WebP — max 4MB. Uploading a new image replaces the current one.</p>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                    Save
                </button>
                <a href="{{ route('admin.beans.index') }}"
                   class="px-4 py-2 rounded-sm border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
