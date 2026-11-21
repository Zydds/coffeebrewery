@props(['title', 'viewRoute'])
<section class="min-w-0" aria-labelledby="{{ Str::slug($title) }}">
    <div class="flex items-center justify-between gap-4">
        <h2 id="{{ Str::slug($title) }}" class="font-display text-2xl font-bold text-coffee-espresso">{{ $title }}</h2>
        <a href="{{ route($viewRoute) }}" class="text-sm font-semibold text-coffee-amber-ink underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber">View all</a>
    </div>
    <div class="mt-4 overflow-x-auto rounded-2xl border border-coffee-mocha/20 bg-coffee-latte">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-coffee-cream text-xs uppercase tracking-wider text-coffee-mocha"><tr class="[&>th]:px-4 [&>th]:py-3 [&>th]:font-semibold">{{ $headers }}</tr></thead>
            <tbody class="divide-y divide-coffee-mocha/15 [&>tr>td]:px-4 [&>tr>td]:py-3">{{ $slot }}</tbody>
        </table>
    </div>
</section>
