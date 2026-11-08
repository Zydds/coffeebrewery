@extends('layouts.public')

@section('title', __('Cafes - Coffeebrewery'))

@section('meta_description', __('Find cafes on the map. Browse by region from Jakarta to Bali, see opening hours and addresses, and get directions.'))

@section('content')
    @php
        // Server-rendered starting value: honest before any JS runs, and the
        // exact state reduced-motion users keep. cafes.js only animates
        // between values on a region change.
        $initialCount = $region ? ($counts[$region->kode] ?? 0) : 0;
    @endphp

    {{-- 24px top gap (the navbar is sticky, not fixed, so nothing needed
         clearing). --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-6 pb-5">
        {{-- Cream header panel: the page sits on latte, so the toolbar gets
             the card surface (cream + mocha border) like the catalog cards. --}}
        <div class="flex flex-wrap items-end justify-between gap-4 rounded-2xl border border-coffee-mocha/40 bg-coffee-cream p-5 sm:p-6">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-coffee-amber text-coffee-espresso shrink-0">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8z"/>
                            <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17"/>
                            <path d="M8 3v2M12 3v2"/>
                        </svg>
                    </span>
                    <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Cafes') }}</h1>
                </div>
                <p class="mt-2 text-coffee-mocha max-w-[560px]">
                    {{ __('Pick a region, explore the map, tap a pin for details.') }}
                </p>

                {{-- Cafe count for the selected city. tabular-nums keeps the
                     width steady while the number counts up (400ms); the
                     sr-only status line carries only the final value so
                     screen readers are not read every intermediate tick. --}}
                <p class="mt-3 flex items-baseline gap-2 text-coffee-mocha">
                    <span id="cafe-count" aria-hidden="true"
                          class="font-display text-2xl font-bold leading-none text-coffee-espresso tabular-nums">{{ $initialCount }}</span>
                    <span aria-hidden="true" class="text-sm">{{ __('cafes in this city') }}</span>
                    <span id="cafe-count-live" role="status" aria-live="polite" class="sr-only">{{ $initialCount }} {{ __('cafes in this city') }}</span>
                </p>
            </div>

            {{-- Provinsi -> kabupaten/kota cascade. Server-rendered so the
                 availability signal ("469 cafes" / "not available yet") is
                 readable before any JS runs; JS rebuilds the kota list
                 when the provinsi changes. --}}
            <div class="flex flex-wrap gap-3" data-cafe-cascade>
                <label class="flex w-full flex-col gap-1 text-sm font-medium text-coffee-mocha sm:w-auto">
                    {{ __('Province') }}
                    <span class="relative block">
                        <select id="cafe-provinsi"
                                class="cafe-select min-h-[44px] w-full appearance-none rounded-xl border-coffee-mocha/40 bg-coffee-latte pr-11 text-sm text-coffee-espresso sm:w-[300px]">
                            @foreach ($provinsi as $p)
                                <option value="{{ $p->kode }}"
                                    {{ $region && $region->parent_kode === $p->kode ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Custom chevron: the forms-plugin arrow is a
                             background image and cannot rotate, so the native
                             arrow is removed and this one flips 180deg via
                             transform when the select opens. --}}
                        <span class="cafe-chevron pointer-events-none absolute inset-y-0 right-3 flex items-center text-coffee-mocha" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </span>
                    </span>
                </label>

                <label class="flex w-full flex-col gap-1 text-sm font-medium text-coffee-mocha sm:w-auto">
                    {{ __('City / Regency') }}
                    <span class="relative block">
                        <select id="cafe-kota"
                                class="cafe-select min-h-[44px] w-full appearance-none rounded-xl border-coffee-mocha/40 bg-coffee-latte pr-11 text-sm text-coffee-espresso sm:w-[460px]">
                            {{-- All 514 kabupaten/kota are rendered once (data-parent
                                 filters them client-side) so every province shows
                                 real names + counts, not just the initial one. --}}
                            @foreach ($kabupatenKota->flatten() as $k)
                                @php $count = $counts[$k->kode] ?? 0; @endphp
                                <option value="{{ $k->kode }}" data-parent="{{ $k->parent_kode }}" data-count="{{ $count }}"
                                    {{ $region && $region->kode === $k->kode ? 'selected' : '' }}>
                                    {{ $k->nama }} · {{ $count ? __(':count cafes', ['count' => $count]) : __('not available yet') }}
                                </option>
                            @endforeach
                        </select>
                        <span class="cafe-chevron pointer-events-none absolute inset-y-0 right-3 flex items-center text-coffee-mocha" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </span>
                    </span>
                </label>
            </div>
        </div>
    </div>

    {{-- Framed map inside the SAME 1200px container as the header card, so
         its edges align with the cream card at any viewport width (20px
         gutters on mobile). `isolate` is load-bearing: Leaflet paints its
         panes at z-index 400-800 (controls 800, progress bar 1000) and
         would otherwise escape this wrapper's stacking context and paint
         OVER the sticky navbar (z-40).
         Leaflet + markercluster load only on this page via the dedicated
         Vite entry (resources/js/cafes.js). The wrapper is also the
         positioning context for the empty overlay and the fetch indicator,
         and overflow-hidden clips tiles/controls into the card corners. --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8">
        <div class="relative isolate rounded-2xl overflow-hidden border border-coffee-mocha/40 shadow-[0_10px_24px_rgba(44,26,20,0.12)]" data-loop>
            <div id="cafe-map" class="h-[68vh] min-h-[420px] w-full bg-coffee-cream" role="region"
                 aria-label="{{ __('Cafe map') }}"></div>

            {{-- Wave-shaped top edge, replacing the flat amber hairline.
                 Always drawn (quiet amber); cafes.js adds .is-loading while a
                 viewport fetch is in flight and the wave brightens/pulses.
                 preserveAspectRatio=none stretches the wave to any width and
                 non-scaling-stroke keeps the line 2.5px on every viewport. --}}
            <svg id="cafe-progress" class="cafe-progress" viewBox="0 0 1200 12" preserveAspectRatio="none"
                 aria-hidden="true" focusable="false">
                <path d="M0 6 Q 75 0 150 6 Q 225 12 300 6 Q 375 0 450 6 Q 525 12 600 6 Q 675 0 750 6 Q 825 12 900 6 Q 975 0 1050 6 Q 1125 12 1200 6"
                      fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                      vector-effect="non-scaling-stroke"/>
            </svg>

            <div id="cafe-empty" hidden
                 class="absolute inset-0 grid place-items-center bg-coffee-cream/90 pointer-events-none">
                <div class="px-6 text-center">
                    <svg class="mx-auto text-coffee-mocha" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8z"/>
                        <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17"/>
                        <path d="M8 3v2M12 3v2"/>
                    </svg>
                    <p class="mt-3 font-medium text-coffee-mocha">{{ __('No cafes in this region yet.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-4 text-sm text-coffee-mocha">
        {{ __('Map data © OpenStreetMap contributors © CARTO') }}
    </div>

    <script>
        window.CAFES_BOOTSTRAP = @json($bootstrap);
    </script>

    @vite(['resources/js/cafes.js'])
@endsection
