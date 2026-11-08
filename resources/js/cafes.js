import L from 'leaflet';
import 'leaflet.markercluster';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
// Page-specific styles come last so they can override Leaflet's defaults.
import '../css/cafes.css';
import { easeOutShared } from './easing';

// ── Cafe map (/cafes) ───────────────────────────────────────
// Single-page map: region cascade, clustered pins, auto-refresh on pan.
// No geolocation ever — the region select is the only navigation input,
// so the page never triggers a browser permission prompt.

const boot = window.CAFES_BOOTSTRAP;

const EASE = 'cubic-bezier(0.22, 1, 0.36, 1)';
const prefersReduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// One shared inline pin for every marker: 26x34, amber body from the
// palette, latte centre dot. ~430 B, no image request, no per-marker HTML.
// Must stay above the init call: function declarations hoist, consts do not.
const PIN_HTML = '<svg width="26" height="34" viewBox="0 0 26 34" aria-hidden="true">'
    + '<path d="M13 1.5c-6.1 0-11 4.8-11 10.8C2 20.5 13 32.5 13 32.5s11-12 11-20.2c0-6-4.9-10.8-11-10.8Z" fill="currentColor"/>'
    + '<circle cx="13" cy="12.3" r="4.3" class="cafe-pin-dot"/></svg>';

if (boot && document.getElementById('cafe-map')) {
    initCafeMap(boot);
}

function initCafeMap(boot) {
    const i18n = boot.i18n;
    const emptyEl = document.getElementById('cafe-empty');
    const barEl = document.getElementById('cafe-progress');

    const map = L.map('cafe-map', {
        center: [-6.2, 106.82],
        zoom: 11,
        zoomControl: true,
        // Read at drag start only (see fitRegion) — soft "rubber-band"
        // containment for the region clamp.
        maxBoundsViscosity: 0.7,
    });

    // CARTO Positron raster tiles (light_all): same OSM data, but the
    // rendering drops the dense POI icon set (mosques, ATMs, shops) so
    // only roads, place labels and our own café pins carry the map —
    // the original complaint. The key is CARTO's client-side basemap
    // key (designed to ship in frontend bundles; optionally domain-lock
    // it in the CARTO dashboard). Free tier requires the © CARTO line,
    // rendered in the corner control here and in the footer below the
    // map.
    L.tileLayer('https://basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png?key=cb1_4zhm_1_bd5940b2878b3cd5f232c77c', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    }).addTo(map);

    // Clustering from day one: city-level zoom has hundreds of pins.
    // The badge colour is a brown ramp keyed to the child count: one or two
    // cafes read as light brown, dense clusters deepen toward the brand's
    // regular brown. Four flat palette steps, never a CSS gradient.
    const clusterIcon = (cluster) => {
        const count = cluster.getChildCount();
        const tier = count >= 200 ? 4 : count >= 50 ? 3 : count >= 10 ? 2 : 1;
        const size = count >= 200 ? 52 : count >= 50 ? 46 : count >= 10 ? 40 : 34;

        return L.divIcon({
            html: `<span class="cafe-cluster-inner">${count}</span>`,
            className: `cafe-cluster cafe-cluster--${tier}`,
            iconSize: [size, size],
        });
    };

    const cluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 45,
        disableClusteringAtZoom: 16,
        iconCreateFunction: clusterIcon,
    });
    map.addLayer(cluster);

    // Shared icon instance for all markers (all 400+ pins reuse this one).
    const pinIcon = L.divIcon({
        className: 'cafe-pin',
        html: PIN_HTML,
        iconSize: [26, 34],
        iconAnchor: [13, 34],
        popupAnchor: [0, -32],
    });

    // Diff-by-id marker bookkeeping — panning must add/remove markers,
    // never clear-and-repaint (that's the classic blinking-map mistake).
    const markers = new Map(); // key -> L.marker
    let fetchSeq = 0;
    let debounceTimer = null;
    let firstFetchDone = false;

    const overlayText = () => emptyEl.querySelector('p');

    const keyOf = (cafe) => cafe.id;

    // Hybrid region clamp (decision C, 2026-11-01): for regions with a bbox
    // the map gets maxBounds = bbox padded ~10% (panning contained with a
    // little give — viscosity 0.7 rubber-bands drags, moveend snaps back)
    // and minZoom = the zoom that still fits the padded box, so the view
    // can't escape to "all of Java" while a city is selected. Regions
    // without a bbox (non-pilot) clear the clamp entirely — free map,
    // never a half-clamped one.
    // Verified against leaflet-src: setMaxBounds(null) clears through the
    // !isValid() branch; maxBoundsViscosity is read only when a drag
    // starts, hence it lives in the L.map init options below.
    function fitRegion(kode) {
        const box = boot.bboxes?.[kode];

        if (box) {
            const raw = L.latLngBounds([[box.s, box.w], [box.n, box.e]]);
            const padded = raw.pad(0.1);

            // Read the floor BEFORE raising minZoom — getBoundsZoom clamps
            // its result to the current min, so the order matters.
            const minZoom = map.getBoundsZoom(padded, false);

            map.setMaxBounds(padded);
            map.setMinZoom(minZoom);
            map.fitBounds(raw, { padding: [30, 30] });
        } else {
            map.setMaxBounds(null);
            map.setMinZoom(0);
        }
    }

    async function refresh() {
        const seq = ++fetchSeq;

        // Slim top-edge progress bar for EVERY fetch (first load and each
        // pan/zoom refresh). Only the response that still owns the latest
        // seq may clear it — a stale reply must not hide a newer in-flight
        // request, so each exit path checks seq before touching the bar.
        barEl?.classList.add('is-loading');

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

        // Keep API translations in the language chosen for this page; the
        // stateless api group cannot read the session.
        params.set('locale', window.APP_LOCALE ?? document.documentElement.lang);

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

                const marker = L.marker([cafe.lat, cafe.lng], { icon: pinIcon });
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

            if (seq === fetchSeq) {
                barEl?.classList.remove('is-loading');
            }
        } catch (e) {
            if (seq === fetchSeq) {
                overlayText().textContent = i18n.loadError;
                emptyEl.hidden = false;
                barEl?.classList.remove('is-loading');
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

    // ── Select chevrons ─────────────────────────────────────
    // Native selects expose no open event, so the arrow follows the
    // interaction: pointerdown/keydown opens it, change/blur/Escape closes
    // it. CSS owns the 150ms rotate (transform only).
    [provinsiSelect, kotaSelect].forEach((select) => {
        if (! select) {
            return;
        }

        const open = () => select.classList.add('is-open');
        const close = () => select.classList.remove('is-open');

        select.addEventListener('pointerdown', open);
        select.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                close();
            } else if (['Enter', ' ', 'ArrowDown', 'ArrowUp'].includes(e.key)) {
                open();
            }
        });
        select.addEventListener('change', close);
        select.addEventListener('blur', close);
    });

    // ── Cafe count ──────────────────────────────────────────
    // Server-rendered value stays put until a region actually changes;
    // then it counts to the new total over 400ms. Reduced motion snaps.
    const countEl = document.getElementById('cafe-count');
    const countLive = document.getElementById('cafe-count-live');
    let countFrame = 0;

    function setCount(kode) {
        if (! countEl) {
            return;
        }

        const option = kode
            ? kotaSelect?.querySelector(`option[value="${CSS.escape(kode)}"]`)
            : null;
        const target = Number(boot.counts?.[kode] ?? option?.dataset.count ?? 0);

        if (countLive) {
            countLive.textContent = `${target} ${i18n.countLabel}`;
        }

        cancelAnimationFrame(countFrame);

        if (prefersReduced()) {
            countEl.textContent = String(target);
            return;
        }

        const from = Number(countEl.textContent) || 0;

        if (from === target) {
            return;
        }

        const start = performance.now();

        const frame = (now) => {
            const t = Math.min((now - start) / 400, 1);

            countEl.textContent = String(Math.round(from + (target - from) * easeOutShared(t)));

            if (t < 1) {
                countFrame = requestAnimationFrame(frame);
            }
        };

        countFrame = requestAnimationFrame(frame);
    }

    // City options are rebuilt in place (same select element), so a new
    // province reads as a swap rather than a silent jump: the control
    // dips to 25% and settles back in 200ms.
    function fadeKota() {
        if (! kotaSelect || prefersReduced()) {
            return;
        }

        kotaSelect.animate(
            [{ opacity: 0.25, transform: 'translateY(-4px)' }, { opacity: 1, transform: 'translateY(0)' }],
            { duration: 200, easing: EASE },
        );
    }

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
        const kode = rebuildKota(provinsiSelect.value);
        fadeKota();
        applyRegion(kode);
    });

    kotaSelect?.addEventListener('change', () => {
        applyRegion(kotaSelect.value);
    });

    function applyRegion(kode) {
        currentRegion = kode || null;
        setCount(kode);

        // URL tracks the selection so back/refresh/shared links behave.
        const url = new URL(window.location.href);

        if (kode && kode !== boot.defaultRegion) {
            url.searchParams.set('region', kode);
        } else {
            url.searchParams.delete('region');
        }

        history.replaceState(null, '', url);

        // Always run fitRegion: a region without a bbox must CLEAR the
        // previous clamp (free map) instead of inheriting it.
        fitRegion(kode);

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
