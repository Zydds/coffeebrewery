import L from 'leaflet';
import 'leaflet.markercluster';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import markerIconUrl from 'leaflet/dist/images/marker-icon.png';
import markerIconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png';

// Bundlers break Leaflet's default-icon path detection (it 404s on
// /marker-icon.png) — point the default icon at the hashed assets.
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIconRetinaUrl,
    iconUrl: markerIconUrl,
    shadowUrl: markerShadowUrl,
});

// ── Cafe map (/cafes) ───────────────────────────────────────
// Single-page map: region cascade, clustered pins, auto-refresh on pan.
// No geolocation ever — the region select is the only navigation input,
// so the page never triggers a browser permission prompt.

const boot = window.CAFES_BOOTSTRAP;

if (boot && document.getElementById('cafe-map')) {
    initCafeMap(boot);
}

function initCafeMap(boot) {
    const i18n = boot.i18n;
    const emptyEl = document.getElementById('cafe-empty');

    const map = L.map('cafe-map', {
        center: [-6.2, 106.82],
        zoom: 11,
        zoomControl: true,
    });

    // Standard OSM raster tiles. Leaflet's built-in attribution control
    // renders the required "© OpenStreetMap contributors" corner for free.
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    // Clustering from day one: city-level zoom has hundreds of pins.
    const cluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 45,
        disableClusteringAtZoom: 16,
    });
    map.addLayer(cluster);

    // Diff-by-id marker bookkeeping — panning must add/remove markers,
    // never clear-and-repaint (that's the classic blinking-map mistake).
    const markers = new Map(); // key -> L.marker
    let fetchSeq = 0;
    let debounceTimer = null;
    let firstFetchDone = false;

    const overlayText = () => emptyEl.querySelector('p');

    const keyOf = (cafe) => cafe.id;

    function fitRegion(kode) {
        const box = boot.bboxes?.[kode];

        if (box) {
            map.fitBounds([[box.s, box.w], [box.n, box.e]], { padding: [30, 30] });
        }
    }

    async function refresh() {
        const seq = ++fetchSeq;

        // Honest overlay: "loading" until the first answer arrives, then
        // either the region's empty state or nothing at all.
        if (! firstFetchDone) {
            overlayText().textContent = i18n.loading;
            emptyEl.hidden = false;
        }

        const b = map.getBounds();
        const params = new URLSearchParams({
            bounds: [
                b.getSouth().toFixed(5),
                b.getWest().toFixed(5),
                b.getNorth().toFixed(5),
                b.getEast().toFixed(5),
            ].join(','),
        });

        if (currentRegion) {
            params.set('region', currentRegion);
        }

        try {
            const response = await fetch(`${boot.apiUrl}?${params}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            // A newer pan started while this one was in flight — drop it.
            if (seq !== fetchSeq) {
                return;
            }

            const body = await response.json();
            const seen = new Set();

            for (const cafe of body.data ?? []) {
                const key = keyOf(cafe);
                seen.add(key);

                if (markers.has(key)) {
                    continue;
                }

                const marker = L.marker([cafe.lat, cafe.lng]);
                marker.bindPopup(popupHtml(cafe));
                markers.set(key, marker);
                cluster.addLayer(marker);
            }

            // Remove markers whose cafés left the viewport.
            for (const [key, marker] of markers) {
                if (!seen.has(key)) {
                    cluster.removeLayer(marker);
                    markers.delete(key);
                }
            }

            firstFetchDone = true;

            // Show the empty state only when the region truly has nothing
            // imported; a region whose cafés are merely out of view stays
            // quiet (the user just pans back). No region = no claim.
            const regionTotal = currentRegion ? (boot.counts?.[currentRegion] ?? 0) : null;
            overlayText().textContent = i18n.emptyRegion;
            emptyEl.hidden = regionTotal === null || regionTotal > 0;
        } catch (e) {
            if (seq === fetchSeq) {
                overlayText().textContent = i18n.loadError;
                emptyEl.hidden = false;
                console.warn('Cafe fetch failed:', e);
            }
        }
    }

    function popupHtml(cafe) {
        const esc = (s) => String(s)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;');

        // Route deep-link: directions mode with the exact coordinates, so
        // Google Maps never lands on a same-named chain somewhere else.
        const routeUrl = `https://www.google.com/maps/dir/?api=1&destination=${cafe.lat},${cafe.lng}`;

        // Area (kecamatan/neighbourhood) and street address are independent
        // signals — merge into one meta line when both exist, since most
        // pins have only one or neither.
        const place = [cafe.area, cafe.address].filter(Boolean).join(' · ');
        const placeLine = place
            ? `<p class="cafe-popup-place">${esc(place)}</p>`
            : '';

        // Raw OSM opening_hours, shown verbatim — no "open now" claims
        // before the Phase 3 parser exists. Absent = simply not shown.
        const hours = cafe.hours
            ? `<p class="cafe-popup-hours">${esc(i18n.hours)}: ${esc(cafe.hours)}</p>`
            : '';

        const updated = cafe.updated
            ? `<p class="cafe-popup-meta">${esc(i18n.updated)}: ${esc(cafe.updated)}</p>`
            : '';

        return `
            <div class="cafe-popup">
                <strong class="cafe-popup-name">${esc(cafe.name)}</strong>
                ${placeLine}
                ${hours}
                <a class="cafe-popup-route" href="${routeUrl}" target="_blank" rel="noopener noreferrer">
                    ${esc(i18n.directions)}
                </a>
                ${updated}
            </div>`;
    }

    // Auto-refresh on pan: debounced, and only after the map settles.
    map.on('moveend', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(refresh, 300);
    });

    // ── Region cascade ──────────────────────────────────────
    let currentRegion = boot.region ?? null;

    const provinsiSelect = document.getElementById('cafe-provinsi');
    const kotaSelect = document.getElementById('cafe-kota');

    // All kabupaten/kota options are server-rendered with data-parent and
    // data-count; switching province just filters them in place, so every
    // province shows real names + counts (and "not available yet" where
    // OSM has nothing imported).
    const kotaOptions = Array.from(kotaSelect.querySelectorAll('option'));

    function rebuildKota(parentKode) {
        const previous = kotaSelect.value;
        let firstAvailable = null;
        let firstBelonging = null;

        kotaOptions.forEach((opt) => {
            const belongs = opt.dataset.parent === parentKode;
            opt.hidden = ! belongs;

            if (belongs && firstBelonging === null) {
                firstBelonging = opt.value;
            }

            if (belongs && firstAvailable === null && Number(opt.dataset.count) > 0) {
                firstAvailable = opt.value;
            }
        });

        // Keep the selection if it still belongs to this province;
        // otherwise jump to the first region that actually has cafés,
        // falling back to the province's first kota — a count-0 region
        // renders the honest "belum tersedia" empty state rather than a
        // blank select.
        const keep = kotaOptions.some((o) => o.value === previous && o.dataset.parent === parentKode);

        kotaSelect.value = keep ? previous : (firstAvailable ?? firstBelonging ?? '');

        return kotaSelect.value;
    }

    provinsiSelect?.addEventListener('change', () => {
        applyRegion(rebuildKota(provinsiSelect.value));
    });

    kotaSelect?.addEventListener('change', () => {
        applyRegion(kotaSelect.value);
    });

    function applyRegion(kode) {
        currentRegion = kode || null;

        // URL tracks the selection so back/refresh/shared links behave.
        const url = new URL(window.location.href);

        if (kode && kode !== boot.defaultRegion) {
            url.searchParams.set('region', kode);
        } else {
            url.searchParams.delete('region');
        }

        history.replaceState(null, '', url);

        if (kode) {
            fitRegion(kode);
        }

        // fitBounds triggers moveend -> refresh; but if the map was
        // already inside the target bounds, no moveend fires.
        refresh();
    }

    // ── Return-state healing ─────────────────────────────────
    // Coming back to this tab (bfcache restore, tab switch, or the map
    // container resizing while away) is the classic "map stuck on the
    // last popup with markers missing" scenario: Leaflet needs a size
    // re-check and the markers need re-diffing against the viewport.
    window.addEventListener('pageshow', () => {
        map.invalidateSize();
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(refresh, 300);
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(refresh, 300);
        }
    });

    // ── First paint ─────────────────────────────────────────
    // The server rendered all 514 kota options; filter them down to the
    // initial province before the user ever opens the select.
    if (provinsiSelect) {
        rebuildKota(provinsiSelect.value);
    }

    if (currentRegion) {
        fitRegion(currentRegion);
    }

    refresh();
}
