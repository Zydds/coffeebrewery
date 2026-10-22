@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="mt-8 flex items-center justify-center gap-4" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="grid h-11 w-11 place-items-center rounded-full border border-coffee-mocha/40 text-coffee-mocha/40 select-none" aria-disabled="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="grid h-11 w-11 place-items-center rounded-full border border-coffee-mocha bg-coffee-cream text-coffee-espresso transition-colors hover:bg-coffee-espresso hover:text-coffee-latte"
               aria-label="Previous page">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                </svg>
            </a>
        @endif

        <span class="text-sm font-medium text-coffee-mocha">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="grid h-11 w-11 place-items-center rounded-full border border-coffee-mocha bg-coffee-cream text-coffee-espresso transition-colors hover:bg-coffee-espresso hover:text-coffee-latte"
               aria-label="Next page">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="grid h-11 w-11 place-items-center rounded-full border border-coffee-mocha/40 text-coffee-mocha/40 select-none" aria-disabled="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                </svg>
            </span>
        @endif
    </nav>
@endif
