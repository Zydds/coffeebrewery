@extends('admin.layout')

@section('title', 'Add Recipe')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Add Recipe</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Create a new brew guide.</p>
    </div>

    <div class="mt-6 max-w-2xl rounded-lg border border-coffee-mocha/30 bg-white p-6">
        <form method="POST" action="{{ route('admin.recipes.store') }}">
            @csrf

            <div>
                <x-input-label for="title" value="Title" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('title')" required maxlength="200" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="coffee_bean_id" value="Coffee Bean" />
                <select id="coffee_bean_id" name="coffee_bean_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    <option value="">Select a coffee bean</option>
                    @foreach ($beans as $bean)
                        <option value="{{ $bean->id }}" @selected(old('coffee_bean_id') == $bean->id)>{{ $bean->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('coffee_bean_id')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="brew_method_id" value="Brew Method" />
                <select id="brew_method_id" name="brew_method_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    <option value="">Select a brew method</option>
                    @foreach ($methods as $method)
                        <option value="{{ $method->id }}" @selected(old('brew_method_id') == $method->id)>{{ $method->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('brew_method_id')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="water_temp_c" value="Water Temperature (°C)" />
                <x-text-input id="water_temp_c" name="water_temp_c" type="number" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('water_temp_c')" min="60" max="100" step="1" required />
                <x-input-error :messages="$errors->get('water_temp_c')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="brew_time_min" value="Brew Time (minutes)" />
                <x-text-input id="brew_time_min" name="brew_time_min" type="number" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('brew_time_min')" min="1" max="120" step="1" placeholder="e.g. 5" />
                <p class="mt-1 text-xs text-coffee-mocha">Total time including prep. Used for the "Quick" filter.</p>
                <x-input-error :messages="$errors->get('brew_time_min')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="grind_size" value="Grind Size" />
                <x-text-input id="grind_size" name="grind_size" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('grind_size')" placeholder="e.g. medium-fine" required maxlength="50" />
                <x-input-error :messages="$errors->get('grind_size')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="ratio" value="Ratio" />
                <x-text-input id="ratio" name="ratio" type="text" class="mt-1 block w-full focus:border-coffee-amber focus:ring-coffee-amber"
                              :value="old('ratio')" placeholder="e.g. 1:15 (18g / 270g)" required maxlength="50" />
                <x-input-error :messages="$errors->get('ratio')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="step_by_step" value="Step-by-Step Instructions" />
                <div x-data="{ items: {{ json_encode(old('step_by_step', [''])) }} }">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 mt-2">
                            <textarea :name="'step_by_step[' + index + ']'" x-model="items[index]" rows="2" maxlength="500"
                                      class="flex-1 rounded border-gray-300 text-sm focus:border-coffee-amber focus:ring-coffee-amber"
                                      placeholder="Describe this step"></textarea>
                            <button type="button" @click="items.splice(index, 1)" class="text-sm text-red-600 hover:underline">Remove</button>
                        </div>
                    </template>
                    <button type="button" @click="items.push('')" class="mt-2 text-sm font-semibold text-coffee-amber hover:underline">+ Add item</button>
                </div>
                <x-input-error :messages="$errors->get('step_by_step')" class="mt-2" />
            </div>

            <div class="mt-4">
                <input type="hidden" name="is_featured" value="0">
                <label for="is_featured" class="inline-flex items-center gap-2">
                    <input id="is_featured" name="is_featured" type="checkbox" value="1"
                           @checked(old('is_featured'))
                           class="rounded border-gray-300 text-coffee-amber focus:ring-coffee-amber">
                    <span class="text-sm font-medium text-gray-700">Feature this recipe</span>
                </label>
                <x-input-error :messages="$errors->get('is_featured')" class="mt-2" />
            </div>

            <div class="mt-4">
                <input type="hidden" name="cheap" value="0">
                <label for="cheap" class="inline-flex items-center gap-2">
                    <input id="cheap" name="cheap" type="checkbox" value="1"
                           @checked(old('cheap'))
                           class="rounded border-gray-300 text-coffee-amber focus:ring-coffee-amber">
                    <span class="text-sm font-medium text-gray-700">Tag as cheap</span>
                </label>
                <x-input-error :messages="$errors->get('cheap')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="px-4 py-2 rounded bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                    Save
                </button>
                <a href="{{ route('admin.recipes.index') }}"
                   class="px-4 py-2 rounded border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
