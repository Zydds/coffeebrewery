@extends('admin.layout')

@section('title', 'Import')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Import</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Create or update coffee beans and recipes from a CSV file.</p>
    </div>

    <div class="mt-6 max-w-2xl rounded-lg border border-coffee-mocha/30 bg-white p-6">
        <form method="POST" action="{{ route('admin.import.preview') }}" enctype="multipart/form-data">
            @csrf

            <div>
                <x-input-label for="type" value="What are you importing?" />
                <select id="type" name="type" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-xs focus:border-coffee-amber focus:ring-coffee-amber text-sm">
                    <option value="beans" @selected(old('type') === 'beans')>Coffee beans</option>
                    <option value="recipes" @selected(old('type') === 'recipes')>Recipes</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="file" value="CSV file" />
                <input id="file" name="file" type="file" accept=".csv,text/csv" required
                       class="mt-1 block w-full text-sm text-coffee-mocha" />
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>

            <p class="mt-3 text-xs text-coffee-mocha">
                UTF-8, comma separated, at most 500 rows and 1 MB. A slug that already exists updates that row
                instead of adding a second one. Put <code>|</code> between values in multi-value columns
                (flavor notes, drinks, methods, steps).
            </p>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                    Preview
                </button>
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded-sm border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 max-w-2xl">
        <h2 class="text-sm font-semibold text-coffee-espresso">Templates</h2>
        <p class="mt-1 text-xs text-coffee-mocha">Each template has the header row plus one example row.</p>
        <ul class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-sm">
            <li>
                <a href="{{ route('admin.import.template', 'beans') }}"
                   class="font-semibold text-coffee-amber hover:underline">Bean template (CSV)</a>
            </li>
            <li>
                <a href="{{ route('admin.import.template', 'recipes') }}"
                   class="font-semibold text-coffee-amber hover:underline">Recipe template (CSV)</a>
            </li>
        </ul>
    </div>
@endsection
