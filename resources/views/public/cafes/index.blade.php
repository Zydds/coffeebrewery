@extends('layouts.public')

@section('title', __('Cafes — Coffeebrewery'))

@section('meta_description', __('Find cafes on the map — browse by region from Jakarta to Bali, see opening hours and addresses, and get directions.'))

@section('content')
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 pt-[88px] pb-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-[clamp(2rem,4vw,2.75rem)]">{{ __('Cafes') }}</h1>
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
                            class="min-h-[44px] w-[220px] rounded-lg border-coffee-mocha/40 bg-coffee-cream text-coffee-espresso">
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
                            class="min-h-[44px] w-[260px] rounded-lg border-coffee-mocha/40 bg-coffee-cream text-coffee-espresso">
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

    {{-- Full-bleed map. Leaflet + markercluster load only on this page via
         the dedicated Vite entry (resources/js/cafes.js). --}}
    <div class="relative border-y border-coffee-mocha/20">
        <div id="cafe-map" class="h-[68vh] min-h-[420px] w-full bg-coffee-cream" role="region"
             aria-label="{{ __('Cafe map') }}"></div>

        <div id="cafe-empty" hidden
             class="absolute inset-0 grid place-items-center bg-coffee-latte/85 pointer-events-none">
            <p class="font-medium text-coffee-mocha px-6 text-center">{{ __('No cafes in this region yet.') }}</p>
        </div>
    </div>

    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-5 text-sm text-coffee-mocha">
        {{ __('Map data © OpenStreetMap contributors') }}
    </div>

    <script>
        window.CAFES_BOOTSTRAP = @json($bootstrap);
    </script>

    @vite(['resources/js/cafes.js'])
@endsection
