@extends('layouts.public')

@section('title', __('Cafes — Coffeebrewery'))

@section('meta_description', __('Find cafes on the map — browse by region from Jakarta to Bali, see opening hours and addresses, and get directions.'))

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px] pb-5">
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
            </div>

            {{-- Provinsi -> kabupaten/kota cascade. Server-rendered so the
                 availability signal ("469 kedai" / "belum tersedia") is
                 readable before any JS runs; JS rebuilds the kota list
                 when the provinsi changes. --}}
            <div class="flex flex-wrap gap-3" data-cafe-cascade>
                <label class="flex flex-col gap-1 text-sm font-medium text-coffee-mocha">
                    {{ __('Province') }}
                    <select id="cafe-provinsi"
                            class="min-h-[44px] w-[220px] rounded-lg border-coffee-mocha/40 bg-coffee-latte text-coffee-espresso">
                        @foreach ($provinsi as $p)
                            <option value="{{ $p->kode }}"
                                {{ $region && $region->parent_kode === $p->kode ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="flex flex-col gap-1 text-sm font-medium text-coffee-mocha">
                    {{ __('City / Regency') }}
                    <select id="cafe-kota"
                            class="min-h-[44px] w-[260px] rounded-lg border-coffee-mocha/40 bg-coffee-latte text-coffee-espresso">
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
                </label>
            </div>
        </div>
    </div>

    {{-- Framed map: 10px side margins + rounded card frame so the map
         reads as part of the catalog instead of a full-bleed embed.
         Leaflet + markercluster load only on this page via the dedicated
         Vite entry (resources/js/cafes.js). The wrapper is the positioning
         context for the empty overlay and the fetch progress bar, and
         overflow-hidden clips tiles/controls into the rounded corners. --}}
    <div class="relative mx-[10px] rounded-2xl overflow-hidden border border-coffee-mocha/40 shadow-[0_10px_24px_rgba(44,26,20,0.12)]">
        <div id="cafe-map" class="h-[68vh] min-h-[420px] w-full bg-coffee-cream" role="region"
             aria-label="{{ __('Cafe map') }}"></div>

        {{-- Slim amber bar at the top edge — visible only while a viewport
             fetch is in flight (cafes.js toggles .is-loading). --}}
        <div id="cafe-progress" class="cafe-progress" aria-hidden="true"></div>

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

    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-5 text-sm text-coffee-mocha">
        {{ __('Map data © OpenStreetMap contributors © CARTO') }}
    </div>

    <script>
        window.CAFES_BOOTSTRAP = @json($bootstrap);
    </script>

    @vite(['resources/js/cafes.js'])
@endsection
