{{-- The home page's only looping animation: a slow word band between the
     catalog and the brew methods. Decorative, so it is hidden from the
     accessibility tree; the same words already appear as real headings.
     data-loop pauses it offscreen (app.js), and reduced motion stops it
     in app.css. --}}
<div class="overflow-hidden border-y border-coffee-mocha/25 bg-coffee-cream py-4" data-loop aria-hidden="true">
    <div class="marquee-track flex w-max">
        @for ($copy = 0; $copy < 2; $copy++)
            <div class="flex shrink-0 items-center gap-8 pr-8">
                @foreach ([__('Coffee beans'), __('Brew Methods'), __('Recipes'), __('Find Your Brew')] as $word)
                    <span class="font-display text-2xl font-bold uppercase tracking-tight text-coffee-espresso">{{ $word }}</span>
                    <span class="h-2 w-2 shrink-0 rounded-full bg-coffee-amber"></span>
                @endforeach
            </div>
        @endfor
    </div>
</div>
