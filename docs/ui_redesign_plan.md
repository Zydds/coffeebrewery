# UI Redesign Plan

**Status (2026-11-01):** Phase 1 (Foundations) ✅ · Phases 2–4 pending · Commerce deferred indefinitely.

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
2. **Phase 2 — Home + Bean detail** (2 commits):
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

## Quirks
- `@custom-variant` **block form uses `@slot`**, not `@content`; the paren form only accepts media-only conditions (`@custom-variant x (@media ...)`), not media+selector — the combined form must be a block with `&:hover { @slot; }`. A wrong form fails **silently** (no build error) — always grep the built CSS.
- Amber text on roast `#4a2c21` ≈ 3.9:1 — OK for large/bold and non-text UI, fails normal text; light surfaces must use amber-ink.
