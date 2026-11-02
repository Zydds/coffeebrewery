# UI Redesign Plan

**Status (2026-11-02):** Phase 1 (Foundations) ✅ · Phase 2 (Home + Bean detail) ✅ · Phases 3–4 pending · Commerce deferred indefinitely.

## Settled decisions
| Fork | Choice |
|---|---|
| Catalog IA | Stays on home (no separate `/beans`) |
| Commerce (cart/checkout) | **Out of scope** — no code exists, not needed now |
| Motion stack | CSS + existing anime.js reveals + View Transitions (no GSAP) |
| Typography | Outfit-only editorial ramp (no serif) |
| Section rhythm | Standardized at 88px (`section-top` utility) |

## Phases
1. **Phase 1 — Foundations** ✅ (one commit): tokens, easing, hover gate, components, slop/contrast sweep. No layout changes.
2. **Phase 2 — Home + Bean detail** ✅ (2 commits):
   - Home: editorial hero + proof strip, band pacing replaces dividers, result count + applied-filter summary, reveal cadence on hero/surprise
   - Bean detail: two-column media/spec sheet, acidity/body meters as graphic (static data, reveal only), "Recipes with this bean" relational section, mobile sticky action bar
3. **Phase 3 — Secondary pages**: recipes index/detail (spec card hero, timeline steps), methods, cheap (**Rp/100g normalized column**), custom
4. **Phase 4 — Motion & a11y deep pass**: shared-element View Transitions (card → detail hero), reveal cadence audit, feel-check round (card lift, VT morph, chip press), full contrast + keyboard audit

Deferred: commerce, AJAX filtering, admin UI pass (internal tool, own sweep later).

## Phase 1 foundations (shipped)
- **`--color-coffee-amber-ink: #a05a32`** — accent TEXT on light (amber `#c87d55` on latte = 3.1:1, fails AA; amber-ink = 6.3:1 latte / 5.6:1 cream). amber = fills/marks/borders + text on espresso/roast only.
- **Radius scale:** 4 `rounded-sm` (checkboxes/meters) · 12 `rounded-xl` (buttons/inputs/tiles) · 16 `rounded-2xl` (cards) · 24 `rounded-3xl` (panels) · full (pills/chips). No `rounded-lg`, no arbitrary px.
- **`--ease-out/-in-out/-drawer`** in `@theme` — overrides Tailwind's weak built-ins site-wide.
- **`@custom-variant hover-fine`** — `(hover: hover) and (pointer: fine)` gate for hover motion/invert; bare `hover:` fires on touch.
- **`@utility section-top`** — 88px rhythm + `scroll-margin-top` for anchor jumps under the sticky nav (replaces all `pt-[88px]`/`pt-[64px]`).
- **`@utility eyebrow`** — uppercase tracked amber-ink label (detail subheads).
- **Components:** `x-chip` (one filter chip for home/recipes/cheap; 150ms color, 120ms press, focus ring, hover-fine), `x-empty-state` (unified dashed box, AA action link), `x-dice-icon` (shared dice glyph).
- **Sweep:** 9 contrast fixes on light surfaces (kept 3 roast-band checkboxes — dark bg, non-text), button ladder + `focus-visible` parity, 6 `<hr>` dividers deleted, radius stragglers migrated, detail h1s → font-display ramp, cheap glued-line fix, cafes selects → 12.

### Verification (P1)
Built-CSS greps: hover-fine media gate ✓, section-top ✓, eyebrow ✓, amber-ink ✓, strong ease-out replaced weak ✓, focus-ring + press rules ✓.
Browser: chips/dice/empty-state render, empty action `rgb(160,90,50)` ✓, cards 16px ✓, `hr` = 0 ✓, rhythm 88px + scroll-margin ✓, consoles clean ✓.
Tests: **52/52 (206 assertions)**.
Note: automation browser runs `prefers-reduced-motion: reduce` — transitions are inert there by design; motion feel-checks belong to Phase 4 on a normal display.

## Phase 2 (shipped)
- **Band pacing system** (home): cream hero → espresso proof strip → latte catalog → cream brew-methods band → roast surprise. The methods band is a full-bleed wrapper div; `#brew-methods` keeps its id + scroll-margin inside it and gains `pb-22` (88px). Surprise lost `mt-[88px]` so bands sit flush — gaps between bands read as bugs, not rhythm. Cheap page got the matching band (required by the card change).
- **`method-card` surface cream → white** so it pops on the cream band; `bean-card` stays cream on the latte catalog (the two never coexist in one section). Verified: white card on `rgb(244,235,225)`.
- **Proof strip**: full-bleed espresso band, site-wide totals via `$proof` in the controller (deliberately NOT paginator totals, which shrink under filters) + gated "Cafe map" pill → `/cafes`. All four labels reuse existing i18n keys.
- **Result count + applied-filter summary** under the catalog search bar: `Showing :from–:to of :total` (en-dash U+2013, byte-matched between Blade and `lang/id.json`), quoted `q`, active category name, amber-ink clear link. Renders `0–0 of 0` + clear on an empty filtered search.
- **Reveal cadence**: hero copy = `data-reveal-stagger` (eyebrow → h1 → sub → CTAs), hero art = `data-reveal`, strip stats cascade stats → link, surprise `data-reveal` → `data-reveal-stagger` (copy column → result card).
- **Bean detail two-column**: media (`aspect-[4/3]`, roast fallback with the card's bean glyph — **13 of 14 beans ship with no image**, the old layout just omitted the image) | spec sheet panel (divided rows: origin, acidity, body, price, recommended drinks; flavor notes below). Grid is one `data-reveal-stagger` → media cascades into panel.
- **Meter numerals**: static `4/5` in font-display beside the dots (reveal only — no count-up; `role="img"` + existing aria keys kept).
- **"Recipes with this bean"** replaces the lone Browse CTA: `$bean->recipes` (relation already existed — no schema work), eager-loaded `recipes.brewMethod` in the controller, cards show title + method chip + ratio/temp/time. Empty state reuses `x-empty-state` with the existing Browse Recipes action.
- **Mobile sticky bar**: `position: sticky; bottom-0` on the LAST child of the page container — pins to the scrollport while scrolling, releases at its flow position above the footer, so **no layout `pb` is needed** (the layout has no class hooks). Hidden at `lg`. Price (or bean name) + "See recipes" → `#bean-recipes` (`scroll-mt-24`).
- **i18n (4 new keys, alphabetical, byte-matched):** `Coffee, explained` (EN in both — editorial display, matches h1 convention), `Showing :from–:to of :total` → `Menampilkan :from–:to dari :total`, `Recipes with this bean` → `Resep dengan biji ini`, `See recipes` → `Lihat resep`. Everything else reused existing keys.
- **Sweep bonus:** 5× `rounded-[10px]` stragglers found in `resources/views/components` (outside the P1 public-views grep scope) → `rounded-xl`: both card Details + View-full-details buttons, navbar menu button.

### Verification (P2)
Build green (`pb-22` = `calc(var(--spacing) * 22)` = 88px; `lg:grid-cols-2`, `sticky`, `lg:hidden`, `aspect-[4/3]`, `scroll-mt-24` all emitted).
Browser: band sequence `rgb(244,235,225)` → `rgb(44,26,20)` → latte → `rgb(244,235,225)` → `rgb(74,44,33)`; strip 14/14/7 + `/cafes` + hover-fine gate + 4-way cascade; count rows for unfiltered/filtered/empty states; detail: spec sheet 5 rows + numerals `4/5`/`3/5`, 2 recipe cards (`1:15`, `85°C`, `7 min`), sticky bar pinned mid-scroll (`barBottom` = `clientHeight`) and released above the footer at page end; ID locale resolves all 4 new keys; consoles clean.
Tests: **52/52 (206 assertions)**.

### Discovered in P2 (fixed in follow-up commit)
- **Navbar horizontal overflow at ~768–830px viewport**: desktop nav (`hidden md:flex` + 5 links + locale switcher) needs ~27px more than the row has at that width → horizontal scrollbar. Pre-existing (P1/P2 only touched the mobile button's radius). Fix = move nav/menu/dropdown breakpoints `md` → `lg` (tablet gets the hamburger).

## Quirks
- `@custom-variant` **block form uses `@slot`**, not `@content`; the paren form only accepts media-only conditions (`@custom-variant x (@media ...)`), not media+selector — the combined form must be a block with `&:hover { @slot; }`. A wrong form fails **silently** (no build error) — always grep the built CSS.
- Amber text on roast `#4a2c21` ≈ 3.9:1 — OK for large/bold and non-text UI, fails normal text; light surfaces must use amber-ink.
- **`data-reveal-stagger` cascades its DIRECT children only.** A wrapper div around the items collapses the cascade to a single tween (caught in P2 verification: the proof strip's stats moved as one until the attribute moved onto the flex container). Put the attribute on the element whose children should cascade.
- **Sticky bottom bar = last child + `position: sticky; bottom: 0`** — pins to the scrollport, releases at flow position above the footer; no `pb` on layout/body needed (works because the page container is the sticky element's parent and has no `overflow`).
