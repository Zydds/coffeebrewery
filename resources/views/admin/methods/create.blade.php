@extends('admin.layout')

@section('title', 'Add Brew Method')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Add Brew Method</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Create a new brewing method.</p>
    </div>

    <div class="mt-6 max-w-2xl rounded-lg border border-coffee-mocha/30 bg-white p-6">
        <form method="POST" action="{{ route('admin.methods.store') }}" enctype="multipart/form-data">
            @csrf

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('name')" required maxlength="150" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="difficulty_level" value="Difficulty" />
                <select id="difficulty_level" name="difficulty_level" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    <option value="easy" @selected(old('difficulty_level') === 'easy')>Easy</option>
                    <option value="medium" @selected(old('difficulty_level') === 'medium')>Medium</option>
                    <option value="hard" @selected(old('difficulty_level') === 'hard')>Hard</option>
                </select>
                <x-input-error :messages="$errors->get('difficulty_level')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="5" required maxlength="2000"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-coffee-amber focus:ring-coffee-amber text-sm">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <input type="hidden" name="is_cheap" value="0">
                <label for="is_cheap" class="inline-flex items-center gap-2">
                    <input id="is_cheap" name="is_cheap" type="checkbox" value="1"
                           @checked(old('is_cheap'))
                           class="rounded border-gray-300 text-coffee-amber focus:ring-coffee-amber">
                    <span class="text-sm font-medium text-gray-700">Show on the Cheap page</span>
                </label>
                <x-input-error :messages="$errors->get('is_cheap')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="equipment_needed" value="Equipment Needed" />
                <div x-data="{ items: {{ json_encode(old('equipment_needed', [''])) }} }">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 mt-2">
                            <input type="text" :name="'equipment_needed[' + index + ']'" x-model="items[index]" maxlength="100"
                                   class="flex-1 rounded border-gray-300 text-sm focus:border-coffee-amber focus:ring-coffee-amber"
                                   placeholder="e.g. gooseneck kettle">
                            <button type="button" @click="items.splice(index, 1)" class="text-sm text-red-600 hover:underline">Remove</button>
                        </div>
                    </template>
                    <button type="button" @click="items.push('')" class="mt-2 text-sm font-semibold text-coffee-amber hover:underline">+ Add item</button>
                </div>
                <x-input-error :messages="$errors->get('equipment_needed')" class="mt-2" />
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
                <a href="{{ route('admin.methods.index') }}"
                   class="px-4 py-2 rounded border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
