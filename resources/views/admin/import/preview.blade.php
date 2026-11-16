@extends('admin.layout')

@section('title', 'Import preview')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Import preview</h1>
        <p class="mt-1 text-sm text-coffee-mocha">
            {{ $name }} &middot; {{ count($preview['rows']) }} rows &middot;
            {{ $type === 'beans' ? 'coffee beans' : 'recipes' }}
        </p>
    </div>

    <div class="mt-4 grid max-w-2xl gap-3 sm:grid-cols-4">
        <div class="rounded-sm border border-coffee-mocha/30 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Ready</p>
            <p class="mt-1 text-xl font-bold">{{ $preview['valid'] }}</p>
        </div>
        <div class="rounded-sm border border-coffee-mocha/30 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">New</p>
            <p class="mt-1 text-xl font-bold">{{ $preview['new'] }}</p>
        </div>
        <div class="rounded-sm border border-coffee-mocha/30 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Updates</p>
            <p class="mt-1 text-xl font-bold">{{ $preview['updates'] }}</p>
        </div>
        <div class="rounded-sm border border-coffee-mocha/30 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-wider text-coffee-mocha">Errors</p>
            <p class="mt-1 text-xl font-bold">{{ $preview['errors'] }}</p>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto rounded-lg border border-coffee-mocha/30 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-coffee-cream text-left text-xs uppercase tracking-wider text-coffee-mocha">
                <tr>
                    <th class="px-4 py-3 font-semibold">Line</th>
                    <th class="px-4 py-3 font-semibold">Slug</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Reason</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-mocha/15">
                @foreach ($preview['rows'] as $row)
                    <tr class="hover:bg-coffee-cream/60">
                        <td class="px-4 py-3 text-coffee-mocha">{{ $row['line'] }}</td>
                        <td class="px-4 py-3 font-medium text-coffee-espresso">{{ $row['key'] ?: '-' }}</td>
                        <td class="px-4 py-3">
                            @if ($row['status'] === 'error')
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Error</span>
                            @elseif ($row['status'] === 'update')
                                <span class="rounded-full bg-coffee-amber/20 px-2 py-0.5 text-xs font-semibold text-coffee-amber-ink">Update</span>
                            @else
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">New</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-coffee-mocha">{{ $row['reason'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-3 max-w-2xl text-xs text-coffee-mocha">
        Only the ready rows are written. A blank optional cell leaves the existing value alone.
        Everything commits in one transaction.
    </p>

    <div class="mt-6 flex items-center gap-3">
        <form method="POST" action="{{ route('admin.import.commit') }}">
            @csrf
            <button type="submit"
                    class="px-4 py-2 rounded-sm bg-coffee-amber text-coffee-espresso text-sm font-semibold hover:bg-coffee-amber/90 transition-colors">
                Confirm import
            </button>
        </form>
        <a href="{{ route('admin.import.create') }}"
           class="px-4 py-2 rounded-sm border border-coffee-mocha text-coffee-espresso text-sm font-semibold hover:bg-coffee-cream transition-colors">
            Cancel
        </a>
    </div>
@endsection
