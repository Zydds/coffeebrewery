@extends('admin.layout')

@section('title', 'Import result')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Import finished</h1>
        <p class="mt-1 text-sm text-coffee-mocha">
            {{ $name }} &middot; {{ $type === 'beans' ? 'coffee beans' : 'recipes' }}
        </p>
    </div>

    <div class="mt-4 grid max-w-2xl gap-3 sm:grid-cols-3">
        <div class="rounded-sm border border-coffee-mocha/30 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Created</p>
            <p class="mt-1 text-xl font-bold">{{ $result['created'] }}</p>
        </div>
        <div class="rounded-sm border border-coffee-mocha/30 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Updated</p>
            <p class="mt-1 text-xl font-bold">{{ $result['updated'] }}</p>
        </div>
        <div class="rounded-sm border border-coffee-mocha/30 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Skipped</p>
            <p class="mt-1 text-xl font-bold">{{ $result['skipped'] }}</p>
        </div>
    </div>

    @if ($result['failed'] !== [])
        <div class="mt-6 max-w-2xl">
            <a href="{{ route('admin.import.failed') }}"
               class="inline-block px-4 py-2 rounded-sm border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
                Download failed rows (CSV)
            </a>
        </div>

        <div class="mt-4 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Line</th>
                        <th class="px-4 py-3 font-semibold">Slug</th>
                        <th class="px-4 py-3 font-semibold">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-coffee-mocha/15">
                    @foreach ($result['failed'] as $row)
                        <tr>
                            <td class="px-4 py-3 text-coffee-mocha">{{ $row['line'] }}</td>
                            <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $row['key'] ?: '-' }}</td>
                            <td class="px-4 py-3 text-coffee-mocha">{{ $row['reason'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6 flex items-center gap-3">
        <a href="{{ route('admin.import.create') }}"
           class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
            Import another file
        </a>
        <a href="{{ $type === 'beans' ? route('admin.beans.index') : route('admin.recipes.index') }}"
           class="px-4 py-2 rounded-sm border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
            View {{ $type === 'beans' ? 'beans' : 'recipes' }}
        </a>
    </div>
@endsection
