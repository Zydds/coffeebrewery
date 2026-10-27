@props(['action', 'placeholder', 'value' => '', 'name' => 'q', 'preserves' => []])

<form method="GET" action="{{ $action }}" class="mt-6 flex max-w-md gap-2" role="search">
    @foreach ($preserves as $pname => $val)
        @if ($val !== null && $val !== '')
            <input type="hidden" name="{{ $pname }}" value="{{ $val }}">
        @endif
    @endforeach

    <div class="relative flex-1">
        <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-coffee-mocha" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/>
            <path d="M20 20l-3.5-3.5"/>
        </svg>
        <label for="search-{{ \Illuminate\Support\Str::slug($placeholder) }}" class="sr-only">{{ $placeholder }}</label>
        <input id="search-{{ \Illuminate\Support\Str::slug($placeholder) }}"
               type="search" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}"
               class="min-h-[44px] w-full rounded-full border border-coffee-mocha/40 bg-coffee-cream pl-11 pr-4 text-sm text-coffee-espresso placeholder:text-coffee-mocha/70 focus:border-coffee-amber focus:outline-none focus:ring-2 focus:ring-coffee-amber/40">
    </div>

    <button type="submit"
            class="min-h-[44px] shrink-0 rounded-full bg-coffee-espresso px-5 text-sm font-semibold text-coffee-latte transition-colors hover:bg-coffee-roast">
        {{ __('Search') }}
    </button>
</form>
