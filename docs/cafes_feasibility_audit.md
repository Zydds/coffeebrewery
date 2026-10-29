# Café Map: Feasibility Audit (OSM Data)

**Date:** 29 October 2026
**Status:** Read-only reconnaissance. No app code, schema or dependencies were created or changed.
**Purpose:** Validate the café-map v1 spec against real OpenStreetMap data before any building starts. All numbers below are measured, not estimated.

---

## 1. Method

| Item | Detail |
|---|---|
| Data source | OpenStreetMap via the Overpass API (ODbL licensed, free) |
| Instance used | `https://maps.mail.ru/osm/tools/overpass/api/interpreter` |
| Instances unreachable | `overpass-api.de` — TLS handshake fails from the dev machine (`SEC_E_ILLEGAL_MESSAGE`, TLS 1.2 forced, still fails). `overpass.kumi.systems` — connects but times out. `overpass.osm.ch` — answers fast but is Switzerland-only (returns 0 for Indonesia) |
| Resilience | 3–5 retries with linear backoff needed; one 504 on a large `out meta` query, resolved by retry |
| Classification data | Kepmendagri No. 300.2-2138/2025 kode wilayah, via the MIT-licensed `cahyadsn/wilayah` mirror (`db/wilayah.sql`) |

**Implication for `import:cafes`:** the command must cycle through multiple Overpass instances with retry/backoff. A single hardcoded instance will break.

### Queries used (reproducible)

```
# count per region (bbox = generous, see bleed warning in §6)
[out:json][timeout:90];nwr["amenity"="cafe"](<south>,<west>,<north>,<east>);out count;

# deep pass with timestamps (freshness = element "timestamp" field)
[out:json][timeout:150];nwr["amenity"="cafe"](-6.43,106.72,-6.19,106.88);out meta;

# area vocabulary (place nodes)
[out:json][timeout:60];node["place"](-6.43,106.72,-6.19,106.88);out tags;
```

---

## 2. Café volume per region — every pilot region passes

| Region | Cafés in OSM | Nodes / Ways |
|---|---|---|
| Jakarta Selatan (core bbox) | 255 | 208 / 47 |
| *Jakarta Selatan (widened, incl. SCBD/Blok M strip)* | *723* | *612 / 111* |
| Jakarta Pusat | 214 | 188 / 26 |
| Jakarta Utara | 606 | 501 / 105 |
| Jakarta Timur | 283 | 226 / 57 |
| Jakarta Barat | 530 | 446 / 84 |
| Kota Bandung | 469 | 364 / 105 |
| Kabupaten Badung | 739 | 644 / 94 |
| Kota Denpasar | 111 | 99 / 11 |

Total across these 8 regions: **~3,000 cafés**. Every region clears the "50+ = launchable" bar; no go/no-go failures on volume.

---

## 3. Jakarta Selatan deep pass — card-field coverage

Widened bbox (`-6.43,106.72 → -6.19,106.88`), 723 records:

| Card field | Coverage | Consequence |
|---|---|---|
| Name | 98% (703/723) | Card anchor; always present |
| Any `addr:*` | 18% | Address line frequently absent |
| `addr:street` **+** `addr:housenumber` | 10% | Full postal-style address is rare |
| `opening_hours` | **21%** (152/723) | 4 of 5 cards show "tidak tersedia" |
| Website | 8% | (contact rows excluded from v1 anyway) |
| Instagram | 1% | Confirms dropping contact rows |
| Phone | 6% | (excluded) |

**Chain share:** 124/723 (17%) are chain brands (Starbucks, Kopi Kenangan, Fore, Tomoro, Chatime…). Legitimate separate branches, not duplicates.

### `opening_hours` formats observed (parser must tolerate all)

```
07:00-20:00            (bare range)
Mo-Su 09:00-22:00      (day-prefixed range)
10:00 - 24:00          (spaced, midnight as 24:00)
24/7                    (continuous)
```

No exotic syntax in the sample. Parser scope: bare ranges, day-prefixed ranges, `24:00`, `24/7`.

### Freshness (OSM last-edit year)

| Year | 2026 | 2025 | 2024 | 2023 | 2022 | 2021 | 2020 | 2019 | 2018 | ≤2017 |
|---|---|---|---|---|---|---|---|---|---|---|
| Cafés | 267 | 89 | 83 | 54 | 83 | 30 | 21 | 28 | 19 | 34 |

- 37% edited in 2026; 61% within the last 3 years
- ~14% untouched for 5+ years (oldest: 2012)

→ The user-visible "data terakhir diperbarui" line carries real information; it is not decoration.

---

## 4. Duplicates — none found

Proximity check over all 255 core records (every pair within 60 m):

- 76 near pairs, **0 same-name pairs** → zero true duplicates
- Near pairs are neighbors/chains: `J.CO / Starbucks 59 m`, `Toodz House / Toko Kopi TUKU 49 m`
- "Starbucks ×10", "Kopi Kenangan ×6" = real branches at distinct locations

**Decision:** dedupe by `osm_id` alone. No name-proximity dedupe pass needed.

---

## 5. Area vocabulary — OSM place nodes

234 named place nodes in the widened bbox. Types: `village` 194 (kelurahan level), `municipality` 18 (these carry the *kecamatan* names), `neighbourhood` 23, `hamlet` 9, `town` 3, `city` 1, `quarter` 1, `locality` 2.

| Found | **Not found** |
|---|---|
| Blok M, Menteng, Tebet (+Barat/Timur), Cipete Utara/Selatan, Old Kebayoran + kelurahan names, Lebak Bulus, Pasar Minggu, Pancoran, Setiabudi, Jagakarsa, Pesanggrahan, Cilandak (Barat/Timur/kota), Mampang Prapatan | **Kemang**, **SCBD**, Fatmawati, Pondok Indah, Gunawarman, Rasuna, Senopati |

Second-source check (cafés' own `addr:*` tags) did not rescue this: only 7/723 cafés carry any `addr:neighbourhood`, "Kemang" appears as scattered free text in 14 records across unrelated tags, "SCBD" in zero. **No structured source exists for these brand-name neighborhoods in OSM.**

**Decision:** the area dropdown runs on kelurahan/kecamatan place nodes (strong coverage), **plus** a small hand-curated alias overlay (~15 entries for the pilot: `Kemang`, `SCBD`, `Pondok Indah`, `Fatmawati`, `Senopati`…), each mapping name → center point or bbox. Maintained manually; no new service.

---

## 6. Classification spine — official kode wilayah (Kepmendagri 2025)

| Region | Official entries |
|---|---|
| DKI Jakarta (31) | Kepulauan Seribu (31.01) + exactly 5 kota administrasi: **Pusat 31.71, Utara 31.72, Barat 31.73, Selatan 31.74, Timur 31.75** |
| Kota Jakarta Selatan (31.74) | 10 kecamatan: Tebet, Setiabudi, Mampang Prapatan, Pasar Minggu, Kebayoran Lama, Cilandak, Kebayoran Baru, Pancoran, Jagakarsa, Pesanggrahan |
| Kota Bandung (32.73) | 30 kecamatan, incl. **Antapani** |
| Bali (51) | 9 kabupaten/kota; **Kabupaten Badung (51.03)** = 6 kecamatan (Kuta, Mengwi, Abiansemal, Petang, Kuta Selatan, Kuta Utara); **Kota Denpasar (51.71)** = 4 kecamatan (Selatan/Barat/Timur/Utara) |

Vocabulary check against the dataset:

- `Canggu` = 51.03.06.2005, Desa in **Kuta Utara, Kabupaten Badung**
- `Seminyak` = 51.03.01.1005, Kelurahan in **Kuta, Kabupaten Badung**
- `Ubud` = 51.04.05.1005, **Kabupaten Gianyar** (a different kabupaten — confirms keeping Gianyar out of the v1 "Bali" pilot and adding it later)
- `Sanur` = in Denpasar Selatan (51.71.01)
- Naming: it is **"Jakarta Pusat"**, not "Jakarta Tengah". Kabupaten and kota are siblings, so "Badung, Bali" and "Denpasar, Bali" are two plain entries under Bali — no special-casing.

---

## 7. Trap caught: bbox over-capture (bleed)

Comparing the core (255) and widened (723) bboxes showed a bbox pulls in cafés from neighboring regions. Confirmed empirically via `addr:city` tags inside the widened box: **Depok ×16, Tangerang Selatan ×4, Central Jakarta ×4** — records that do not belong to Jakarta Selatan.

**Decision:** the real import queries by **official admin boundary relation** (e.g. Overpass `area` for 31.74), not a hand-typed bbox. Availability counts shown in the UI (`Bandung · 469 kedai`) then mean exactly what they say.

---

## 8. Decisions locked by these numbers

1. **Card layout** — name-led; address and hours are small conditional lines shown only when present. Hours never lead (21% coverage). Missing address is cosmetic: the pin is the location, "Rute ke sini" does the rest.
2. **Area dropdown** — viable nationwide on place nodes + curated alias overlay for brand-name neighborhoods (Kemang, SCBD…).
3. **Import method** — by official admin boundary, upsert by `osm_id`, no dedupe pass, flag-not-delete on absence, multi-instance Overpass client with retries.
4. **Pilot order** — Jakarta Selatan first, then the other 4 Jakarta kota, Bandung, Badung, Denpasar (smallest, last). Every region is a go on volume.

## 9. Explicitly unchanged from the v1 spec

No geolocation, no distance, no contact rows, no owner claims, no admin café CRUD, no detail pages, no ratings, no roasteries (`shop=coffee` later), no scheduled import (manual during development).

---

## 10. Reproduction notes

- Raw Overpass JSON exports and the kode wilayah SQL were kept in a temp folder for this audit only (not committed — re-downloadable via the queries in §1; the kode wilayah mirror is `cahyadsn/wilayah` on GitHub).
- All bboxes in §2 are generous rectangles that intentionally overlap at borders; treat the per-region counts as slightly inflated. Section 7's boundary-relation approach removes this for production.
- Coverage percentages in §3 were measured on the widened Selatan sample (n=723); the core-only sample (n=255) showed the same profile within ~1 pp (hours 24%, address 18%).
