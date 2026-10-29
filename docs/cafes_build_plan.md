# Café Map: Build Plan (inside Coffeebrewery)

**Goal:** `/cafes` — single-page café discovery map for Indonesia, inside the existing Coffeebrewery Laravel app.
**Spec:** converged v1 spec (conversation) + `docs/cafes_feasibility_audit.md` (measured numbers).
**Rule:** module-clean — café code never touches `CatalogController`, catalog views or catalog models. A future split-out stays possible.

**Stack additions only:** `leaflet` + `leaflet.markercluster` (npm/Vite). No backend packages. No PostGIS (bbox queries work on SQLite tests and Postgres prod alike).

---

## Phase 1 — Data pipeline (no UI)

Everything downstream reads from a table this phase fills. The audit's numbers act as a checksum: a correct Jakarta Selatan import must land in the **255–723** range depending on boundary strictness; ~40 or ~2,000 means the boundary resolution is wrong.

**Tasks**
1. Migrations + models (3 new tables):
   - `cafes` — `osm_id` + `osm_type` (unique together; node 1 and way 1 can both exist), `name`, `lat`, `lng` (decimal 10,7), `region_kode` (e.g. `31.74`), `area_name` (nullable), `opening_hours` (raw text), `addr_street`, `addr_housenumber`, `addr_city`, `tags` (json), `osm_timestamp` (OSM last edit), `last_seen_at` (last import), `is_missing` (flag, never delete)
   - `wilayah` — `kode` (PK), `nama`, `level` (`provinsi`|`kota`), `parent_kode`. **Levels 1–2 only** (~552 rows) — that's all the cascade needs; kecamatan lives in the in-map area dropdown
   - `cafe_areas` — OSM place nodes per region: `osm_id`, `name`, `place_type`, `lat`, `lng`, `region_kode`, `is_alias`
2. `php artisan wilayah:import` — parse the Kepmendagri 2025 dataset (`cahyadsn/wilayah` mirror), insert levels 1–2
3. `app/Services/Cafes/OverpassClient.php` — **multi-instance with retry/backoff** (`overpass-api.de` is unreachable from this machine; `maps.mail.ru` is the confirmed-working instance; cycle 3+, linear backoff, tolerate 504s)
4. Boundary resolution: resolve each kab/kota to an Overpass `area` by full official name + admin level (`"Kota Jakarta Selatan"`, `"Kabupaten Badung"`); log the area id chosen per region; ambiguity guard (multiple matches → pick nearest to expected coords, warn)
5. `php artisan import:cafes --region=31.74`:
   - Query `nwr["amenity"="cafe"](area.X)` with `out meta center`
   - **Upsert** by (`osm_id`,`osm_type`) → update `last_seen_at`; new → insert
   - After run: flag region's absent cafés `is_missing = true` (**flag, never delete** — protects against a flaky response wiping a region)
   - Query place nodes for the region → `cafe_areas`
   - Assign each café its nearest place node within a radius (start **1 km**, tune after first import)
   - Print coverage report per region: count, % hours, % address, freshness spread — every run re-checks against the audit baseline

**Done when:** pilot import of Jakarta Selatan completes, count matches audit, report prints, re-running the command twice changes nothing (idempotent), and a second run after deleting one café from the fixture flags it instead of deleting.

---

## Phase 2 — Map core (first visible milestone)

**Tasks**
1. `CafeController@index` → `resources/views/public/cafes/index.blade.php`, route `GET /cafes` in `routes/web.php`
2. Public API `GET /api/cafes?bounds=s,w,n,e&region=31.74` → JSON (bbox `BETWEEN` + `region_kode`, `is_missing = false`, cap ~500 rows)
3. Leaflet via npm/Vite (not CDN — matches the Vite pipeline), OSM raster tiles, default attribution corner ships automatically
4. Server-render the provinsi → kabupaten/kota cascade (from `wilayah`, grouped counts: `Bandung · 469 kedai` / `belum tersedia`) — Alpine enhances, no extra request for first paint
5. **Clustering** (`leaflet.markercluster`) from the start — Selatan has hundreds of pins at city zoom
6. Default region: **31.74 Jakarta Selatan**; URL tracks it (`?region=31.74`) via `history.replaceState`
7. Auto-refresh on `moveend` — **300 ms debounce, diff markers by (`osm_id`,`osm_type`)**, add/remove only (no clear-and-repaint blink)

**Done when:** `/cafes` loads showing clustered Selatan pins, panning re-queries with no marker blinking, cascade switches regions and re-fits the map, back/refresh/shared URL restores the same view, console clean.

---

## Phase 3 — The card (the actual product)

**Tasks**
1. `app/Services/Cafes/OpeningHoursParser.php` — the 4 audit-confirmed formats: `07:00-20:00`, `Mo-Su 09:00-22:00`, `10:00 - 24:00`, `24/7`. Output localized: `07.00–20.00` (ID-style dots), `Buka 24 jam`, or `Jam buka tidak tersedia`
2. Card content component, one at a time, close on × or map tap:
   - Name (always — 98% coverage)
   - Type chips from a **fixed allowlist** of OSM tags (e.g. `roastery`, `wifi`, `outdoor_seating`, `takeaway`, `cuisine=coffee_shop`)
   - Address — **conditional line**, shown only when `addr:*` exists (18%)
   - Hours — **conditional line** (21%), never the card's lead
   - `Data terakhir diperbarui: <osm_timestamp>` (admin-override path does not exist in v1, so this is always OSM time)
   - **`Rute ke sini`** → `google.com/maps/dir/?api=1&destination=LAT,LNG` (coords, never name search)
3. Presentation: **desktop → anchored popup; mobile (<768px) → draggable bottom sheet**; same content component, two shells
4. anime.js slide for the sheet via the existing `play()` choke point (optional polish, not a blocker)

**Done when:** pin tap shows the card on both desktop and mobile widths, all 4 hours formats render correctly (unit-tested), missing hours/address render the honest fallback, route link opens Google Maps in directions mode at the exact pin.

---

## Phase 4 — Area navigation (in-map dropdown)

**Tasks**
1. Top-right dropdown in the map: areas **in the current region with ≥1 café** (from `cafe_areas` + counts) — the audit's "only show what exists" rule
2. Select → `flyTo` area center + filter pins to that area; clearing restores region view
3. `config/cafe_area_aliases.php` — ~15 curated entries the audit proved missing from OSM: **Kemang, SCBD, Pondok Indah, Fatmawati, Senopati…** (name + center/bbox), merged into the dropdown as pseudo-areas; cafés matched to aliases by point-in-radius at query time
4. Kecamatan-level names ride along here (they're already in `cafe_areas` as `place_type=municipality`)

**Done when:** dropdown lists only areas that have cafés, selecting Kemang (alias) zooms+filters correctly, kelurahan areas (Cipete Selatan…) work identically.

---

## Phase 5 — Polish & ship

**Tasks**
1. i18n: all new strings into `lang/en.json` + `lang/id.json` — UI labels/actions **Indonesian**, per the app's copy policy
2. SEO: `/cafes` meta, canonical, OG/Twitter (reuse the `layouts/public.blade.php` section pattern — remember `yieldContent` pre-escapes: `{!! !!}`)
3. Legal block in footer: `© OpenStreetMap contributors` + ODbL note + tile-provider line. Belt-and-suspenders with Leaflet's built-in corner
4. Feature tests (`RefreshDatabase`, SQLite, Overpass **fixture JSON** — no live network in tests):
   - `ImportCafesTest` — upsert idempotency, flag-not-delete, coverage report output
   - `CafeQueryTest` — bbox filtering, region filter, missing-flag exclusion, row cap
   - `CafePageTest` — 200, cascade options present with counts, attribution string present
   - `OpeningHoursParserTest` (Unit) — all 4 formats + garbage input
5. Browser verification: console clean, mobile viewport card, clustering feel, reduced-motion unaffected
6. `npm run build` + checkpoint commits (**ask before pushing**)

**Done when:** `php artisan test` green, both locales render, footer attribution present, zero console errors, prod-ready build assets.

---

## Decisions to settle during the build (not blockers)

| # | Decision | Lean |
|---|---|---|
| 1 | Tiles: OSM standard raster vs MapTiler free tier | OSM standard for pilot (zero setup); revisit if tile rate limits bite |
| 2 | Hours: schedule text only, or computed "Buka sekarang" badge | Schedule text v1; badge only if parser confidence is high on the sample |
| 3 | Which OSM tags become card chips | Fixed allowlist, decided against the imported Selatan data |
| 4 | Area-assignment radius | 1 km → tune from first-import distribution |
| 5 | Boundary ambiguity handling | Verify per region during pilot; warn loudly, never guess silently |

## Explicitly out of scope for v1 (unchanged)

Geolocation · distance · contact rows · owner claims · admin café CRUD · detail pages · ratings/reviews · roasteries (`shop=coffee`) · scheduled imports · city landing pages.

## Known environment quirks

- `overpass-api.de` unreachable from the dev machine (TLS) — hence the multi-instance client
- Tests run SQLite — bbox queries chosen deliberately to keep them runnable
- Prod = Neon Postgres; app server should deploy in Neon's region
- `yieldContent()`/`startSection()` pre-escape with `e()` — JSON-LD/meta blocks need `{!! !!}`
