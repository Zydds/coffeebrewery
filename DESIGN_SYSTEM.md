# Coffeebrewery Design System

Verified against `resources/css/app.css`, `resources/js/app.js`, every component in `resources/views/components/`, every layout in `resources/views/layouts/`, and the public pages. Tailwind is v4.3.3 with CSS-first tokens; there is no `tailwind.config.js`.

## 1. Principles

- **Flat vector.** Inline SVG, flat fills, no gradients, textures, bevels, or 3D. Illustration uses the same seven palette tokens as the UI.
- **Flowing water.** Waves divide bands, soften card edges, and carry the hero. Motion is slow, lateral, and continuous only where it explains the theme.
- **Minimal motion.** Motion exists for feedback, state change, or first-view reveal. One looping animation per page maximum.
- **Fixed palette.** Seven `coffee-*` colors plus opacity variants. No new color enters without approval.

## 2. Color palette

| Token | Hex | Where used | Contrast against usual background |
|---|---|---|---|
| `--color-coffee-espresso` | `#2C1A14` | Body text, navbar/footer/proof/surprise surfaces, buttons, active chips | `16.06:1` on latte; `14.09:1` on cream; latte text on it `16.06:1` |
| `--color-coffee-mocha` | `#6F4E37` | Body and meta text, borders, dividers, inactive dots | `7.20:1` on latte; `6.32:1` on cream |
| `--color-coffee-latte` | `#FDFBF7` | Page background, inverse text, quiet panels | Text on espresso `16.06:1`; on roast `12.14:1` |
| `--color-coffee-amber` | `#C87D55` | Fills, marks, borders, icons, active underline, text on dark only | Text on espresso `5.15:1`; on roast `3.90:1`; on latte `3.12:1` (non-text only) |
| `--color-coffee-amber-ink` | `#A05A32` | Accent text on light surfaces, focus rings, links | `5.08:1` on latte; `4.45:1` on cream |
| `--color-coffee-cream` | `#F4EBE1` | Section bands, cards, inverse text | Text on espresso `14.09:1`; on roast `10.65:1` |
| `--color-coffee-roast` | `#4A2C21` | Dark bands, media blocks, detail overlay | Latte text `12.14:1`; cream text `10.65:1` |

Opacity variants in use: `/10`, `/15`, `/20`, `/25`, `/30`, `/40`, `/50`, `/70`, `/80`, `/85`, `/90`. `espresso` on `amber` is `5.15:1`. Hex values in the code comments do not always match computed ratios; see Cleanup.

## 3. Typography

Families: **Outfit** for display/headings, loaded 600, 700, 800; **Inter** for UI/body, loaded 400, 500, 600. Both come from Bunny Fonts in `layouts/public.blade.php`.

| Role | Family | Size / line-height | Weight | Notes |
|---|---|---|---|---|
| Home h1 | Outfit | `clamp(2.6rem, 6vw, 4.5rem)` / `1.1` | 800 | `tracking-tight`; one highlighted phrase in an amber `mark` |
| Page h1 | Outfit | `clamp(2rem, 4vw, 2.75rem)` / inherited `1.5` | 700 | Cafes, Cheap, Custom, Recipes index |
| Detail h1 | Outfit | `clamp(2rem, 4vw, 3rem)` / `1.1` | 800 | Bean, method, recipe detail; `tracking-tight` |
| Section h2 | Outfit | `clamp(2rem, 4vw, 2.75rem)` / inherited `1.5` | 700 | Catalog and band headings |
| Subsection h2 | Outfit | `clamp(1.75rem, 3vw, 2.25rem)` / `1.25` | 700 | “Recipes with this bean” |
| Card h3 | Outfit | `1.125rem` / `1.375` | 700 | `leading-snug` |
| Body | Inter | `1rem` / `1.5` | 400 | Default body |
| Body large | Inter | `1.125rem` / `1.75` | 400 | Hero paragraph |
| Small | Inter | `0.875rem` / `1.25` | 400–600 | Meta, labels, buttons, helper text |
| Caption / eyebrow | Outfit | `0.75rem` / `1.5` | 600 | Uppercase, `0.14em` tracking, `amber-ink` |
| Numeric | Inter | `15px`–`2.25rem` / `1` | 700 | Prices and stats use `tabular-nums` |
| Navbar wordmark | Outfit | `1.5rem` / `2rem` | 700 | 40px amber tile + 24px cup icon |

## 4. Spacing, radius and shadow scale

Spacing uses Tailwind’s `0.25rem` base. Values in use: `2px` (`0.5`), `4px` (`1`), `6px` (`1.5`), `8px` (`2`), `10px` (`2.5`), `12px` (`3`), `16px` (`4`), `20px` (`5`), `24px` (`6`), `32px` (`8`), `40px` (`10`), and `88px` (`section-top`, `pb-22`). Container gutters are `20px` and `32px` from `sm`.

| Radius | Value | Use |
|---|---|---|
| `rounded-sm` | `4px` | Meters, small spec cells |
| `rounded-md` | `6px` | Checkboxes, legacy Breeze controls |
| `rounded-lg` | `8px` | Legacy Breeze dashboard, admin, modal, and guest views |
| `rounded-xl` | `12px` | Buttons, inputs, small tiles, quiz options |
| `rounded-2xl` | `16px` | Cards, map frame, page header panels |
| `rounded-3xl` | `24px` | Hero media, quiz shell, result panel |
| `rounded-full` | `9999px` | Chips, badges, dots, pagination, locale toggle |

| Shadow | Value | Use |
|---|---|---|
| Navbar hairline | `0 1px 0 rgba(253,251,247,0.14)` | Sticky navbar edge |
| Card hover | `0 10px 24px rgba(44,26,20,0.15)` | Bean/method card lift |
| Map frame | `0 10px 24px rgba(44,26,20,0.12)` | Cafe map wrapper |
| Sticky action bar | `0 -8px 24px rgba(44,26,20,0.08)` | Mobile bean bar |
| Cluster | `0 4px 10px rgba(44,26,20,0.18)` | Map clusters |
| Popup | `0 10px 24px rgba(44,26,20,0.15)` | Cafe popup |

Default surfaces rely on `1px` mocha borders, not elevation. Legacy Breeze views still use `shadow-sm`, `shadow-md`, and `shadow-xl`.

## 5. Layout

Tailwind default breakpoints: `sm 40rem/640px`, `md 48rem/768px`, `lg 64rem/1024px`, `xl 80rem/1280px`, `2xl 96rem/1536px`.

- **Public container:** `max-w-[1200px]`, centered, `px-5` on mobile and `sm:px-8` from 640px.
- **Narrow detail container:** `max-w-4xl` (`896px`) with `px-4 sm:px-6 lg:px-8`; recipes and methods detail.
- **Admin/app container:** Breeze `max-w-7xl` (`1280px`) with `px-4 sm:px-6 lg:px-8`.
- **Text measures:** `max-w-[420px]`, `[460px]`, `[480px]`, `[560px]`; quiz column `max-w-2xl` (`672px`).

Grids: home beans are a two-card mobile snap carousel, then `md:grid-cols-3` and `lg:grid-cols-4`; methods `2 / md:3 / lg:4`; recipes `1 / sm:2 / lg:3`; bean recipes `1 / sm:2`; quiz options `1 / sm:3` (drink `1 / sm:2`); hero `1 / lg:[1.1fr_.9fr]`; Surprise `1 / lg:[.85fr_1.15fr]`.

Band rhythm: **cream** hero → **espresso** proof strip → **latte** catalog → **cream** methods → **roast** Surprise → **espresso** footer. Cheap mirrors that with a cream header card, latte catalog, cream methods, roast Surprise. Cafes sit on latte with a cream toolbar panel and a latte map card; custom sits on latte with a cream quiz shell and a roast result panel. Detail pages sit on latte with cream spec cards, roast media, white spec cells, and an espresso footer. Section tops use `section-top` = `5.5rem` (`88px`); hero uses `py-16 lg:py-[72px]`, detail pages `py-12`, cafes `pt-6 pb-5`.

## 6. Components

| Component | File | Variants | States | Blade usage |
|---|---|---|---|---|
| Navbar | `components/navbar.blade.php` | Desktop / mobile | Active route, hover, mobile open | `<x-navbar />` |
| Language toggle | `components/locale-switcher.blade.php` | EN / ID | Current (`amber` fill), inactive, focus | `@include('components.locale-switcher')` |
| Buttons | `components/primary-button.blade.php`, `secondary-button.blade.php`, `danger-button.blade.php`; public CTAs are inline | Primary, secondary, danger, public CTA | Hover, active scale, `focus-visible`, disabled | `<a class="min-h-[52px] rounded-xl bg-coffee-espresso px-7 font-semibold text-coffee-latte">…</a>` |
| Filter pills | `components/chip.blade.php` | Active / inactive | Hover invert, press `scale-[0.97]`, focus ring, `aria-current` | `<x-chip :href="route('home')" :active="true">All</x-chip>` |
| Search field | `components/search-bar.blade.php` | Default | Placeholder, focus ring, submit | `<x-search-bar :action="route('home')" :placeholder="__('Search beans…')" />` |
| Bean card | `components/bean-card.blade.php` | Bean | Closed, open overlay, hover, image/fallback | `<x-bean-card :bean="$bean" x-reveal class="reveal" />` |
| Brew method card | `components/bean-card.blade.php` | Method | Same shell, 3 dots, open overlay | `<x-bean-card :method="$method" x-reveal class="reveal" />` |
| Recipe card | `public/recipes/index.blade.php`, `public/beans/show.blade.php` | Cheap, non-cheap, featured | Hover border/title, focus | `<a href="{{ route('recipes.show', $recipe->slug) }}" class="recipe-card …">…</a>` |
| Badges | `public/recipes/index.blade.php`, `public/recipes/show.blade.php` | Cheap, non-cheap, featured | Static | `<span class="rounded-full bg-coffee-amber px-2 py-0.5 text-xs font-semibold text-coffee-espresso">Cheap</span>` |
| Acidity / difficulty dots | `components/bean-card.blade.php`, `public/beans/show.blade.php` | 5 bean dots / 3 method dots | On, off, staggered fill on reveal | `@for ($i = 1; $i <= 5; $i++) <i class="h-2.5 w-2.5 rounded-full border-2 border-coffee-mocha {{ $i <= $level ? 'bg-coffee-espresso' : '' }}"></i> @endfor` |
| Carousel | `public/home.blade.php` | Mobile 2-card snap / desktop grid | Overflow, end fade, keyboard tab stop | `<div class="flex snap-x snap-mandatory gap-6 overflow-x-auto md:grid md:grid-cols-3">…</div>` |
| Pagination | `components/page-arrows.blade.php` | Previous/next enabled / disabled | Hover, focus, disabled | `<x-page-arrows :paginator="$recipes" />` |
| Quiz option card | `public/custom/index.blade.php` | Unselected / selected / dimmed | Hover border, focus outline, `aria-pressed` | `<button type="button" :aria-pressed="answers.time === 'quick'" class="quiz-option …">…</button>` |
| Progress bar | `public/custom/index.blade.php` | Future / active / past segment | `scaleX(0→1)`, reduced-motion snap | `<div class="h-1.5 w-8 rounded-full bg-coffee-mocha/20"><div class="quiz-seg-fill …"></div></div>` |
| Surprise me panel | `components/surprise-section.blade.php` | Default / `cheapOnly` | Loading, error, empty, rolled | `<x-surprise-section />` or `<x-surprise-section cheap-only />` |
| Map container | `public/cafes/index.blade.php`, `resources/js/cafes.js` | Ready / loading / empty | Fetch wave, cluster tiers, popup | `<div id="cafe-map" class="h-[68vh] min-h-[420px]" role="region" aria-label="{{ __('Cafe map') }}"></div>` |
| Footer | `layouts/public.blade.php` | Single | Static | `<footer class="bg-coffee-espresso text-coffee-cream">…</footer>` |

Selected values: navbar is `72px` tall, sticky at `z-40`, links `44px`; locale links are `36px` high. Bean card: `16px` radius, `96px`/`112px` image block, `p-4` body, `44px` Details button; overlay is roast with `p-5`, `200ms` fade. Recipe card: `16px` radius, `p-5`, `sm:grid-cols-4` stats. Pagination circles are `44px`. Map is `68vh` / `min 420px` inside a `16px` frame.

## 7. Interaction rules

**Current code:** bean and method cards **do have a hover effect**: `.card-lift` transitions `translate`, shadow, and border over `200ms`; the class also applies `hover-fine:-translate-y-1`, an amber border, and a `0 10px 24px` shadow. Recipe cards change border and title color only. The brief’s “cards have no hover effect” is **not implemented**; see Cleanup.

**Details behaviour (implemented):** clicking Details sets `open = true`, hides the button with `opacity: 0`, and fades the absolute overlay in over `200ms` via `opacity` and `visibility`. The overlay’s full-detail link receives focus after the next tick. Moving the pointer out of the card closes the overlay unless the device reports `(hover: none)`. Clicking the overlay closes it. `Escape` closes it and restores focus to the Details button. The button remains focusable while transparent; the overlay is a `role="group"`, not a dialog, and has no focus trap. Hover-close while focus is inside the overlay can leave focus on a hidden link.

## 8. Motion

| Token / value | Duration | Use |
|---|---|---|
| `--ease-out` | `cubic-bezier(0.22, 1, 0.36, 1)` | The shared curve; CSS and JS mirror it by hand |
| `--ease-in-out` | `cubic-bezier(0.77, 0, 0.175, 1)` | Legacy only |
| `--ease-drawer` | `cubic-bezier(0.32, 0.72, 0, 1)` | Legacy only |
| `--duration-reveal` | `700ms` | One-shot reveal |
| UI feedback | `150–200ms` | Hover, press, border, shadow, popup |
| Reveal exit | `450ms` | Leaving viewport |
| Dot fill | `360ms`, `40ms` stagger | Acidity/difficulty dots |
| Quiz segment | `400ms`, `scaleX` | Active progress segment |
| Recipe leave / settle | `120ms` / `250ms` | Grid fade and 16px slide |
| Check draw | `150ms` | Surprise checkbox |
| Surprise swap | `250ms` out/in; steps `300ms` / `50ms` stagger | Result card |
| Finder step / result | `200ms` out, `250ms` in, `350ms` result | Quiz |
| Count-up | `900ms` stats; `400ms` cafe count | rAF loops |
| Hero drift | `40s` back, `30s` front, linear | Home loop |
| Cafe loading wave | `1s` alternate, opacity only | Cafes loop |

**Allowed properties:** the design rule is `transform` and `opacity` only. The current code also transitions `border-color`, `box-shadow`, `background-color`, `color`, `visibility`, and `stroke-dashoffset`; these are existing feedback exceptions, not permission for new ones.

**Reveal:** `x-reveal` uses `.reveal`: `opacity 0 → 1` and `translateY(12px) → 0` over `700ms`, one-shot, via IntersectionObserver with `rootMargin: 0 0 -60px`. `data-reveal` tweens the element itself; `data-reveal-stagger` cascades direct children at `60ms`, entering with `24px` and exiting over `450ms` with `24px`. All of it gates on `html.js-motion`; without JS the end state is visible.

**Wave divider:** `components/wave-divider.blade.php` is a static inline SVG, `viewBox="0 0 1440 100"`, default height `96px`, `preserveAspectRatio="none"`. `from-color` paints above the wave and `to-color` fills it; both are whitelisted to the seven tokens. `flip` applies `scaleX(-1)`. It is decorative and `aria-hidden`.

**One loop per page:** home uses the two hero wave bands; cafes uses the fetch wave pulse while loading. Both pause offscreen via `data-loop` + IntersectionObserver.

**Reduced motion:** `prefers-reduced-motion: reduce` kills all animations and transitions globally, forces `.reveal` visible, disables view transitions, makes the JS `play()` helper return without tweening, leaves server-rendered count values in place, and snaps the quiz fill to `scaleX(1)`.

## 9. Iconography and illustration

No icon package or icon font. All icons are inline SVG using `currentColor`, `stroke-linecap="round"`, `stroke-linejoin="round"`, and decorative `aria-hidden="true"`. Stroke widths in use: `1.2`, `1.5`, `1.8`, `2`, `2.5`, `3.5`.

Sizes: `10`, `15`, `16`, `18`, `20`, `22`, `24`, `44`, `120`; clusters are `34/40/46/52px` with `12/13/14px` numerals. The shared cafe pin is one inline SVG of about `430 B` reused for all markers. Hero cup and beans are flat-vector illustration using the palette only.

**Rule:** an inline SVG stays under `2 KB`. Current exceptions: `application-logo.blade.php` is `3,045 B`; the wave divider is `1,736 B`; the dice icon is `672 B`.

## 10. Accessibility

- **Contrast:** target WCAG AA, `4.5:1` for normal text and `3:1` for large text and non-text UI. Computed ratios are in section 2. `amber-ink` on cream is `4.45:1`, just under AA for normal text; `amber` on latte is `3.12:1`, so it is not body text there.
- **Focus:** public controls use `focus-visible:ring-2` with `ring-coffee-amber-ink`, offset `2px` on latte/cream; dark surfaces use `ring-coffee-latte`; quiz options use `outline-2` with `outline-offset-2`. Legacy Breeze components still use indigo rings.
- **Keyboard cards:** Details is a real button with `aria-controls` and `aria-expanded`; the overlay link is reachable; `Escape` closes and restores focus. Recipe cards and filter chips are links. The bean carousel becomes a tab stop (`tabindex="0"`) only when it overflows and scrolls with the browser’s native keys; there are no custom arrow handlers.
- **Touch targets:** most controls use `min-h-[44px]` or `44px` squares. Exceptions: locale links are `36px`, recipe serving buttons are `40×40px`.
- **Semantics:** `aria-current="page"` on active chips, `aria-pressed` on quiz/serving toggles, `aria-live` on results and cafe counts, `role="region"` on the map, `sr-only` labels for dots and icon-only controls.
- **Reduced motion:** required and implemented globally; see section 8.

## 11. Do and do not

| Do | Reason |
|---|---|
| Use `coffee-*` tokens and opacity variants | Keeps the fixed palette intact. |
| Keep interactive controls at `44px` minimum | Prevents missed taps. |
| Gate hover motion with `hover-fine` | Touch devices otherwise latch `:hover`. |
| Keep one looping animation per page | More loops compete and raise motion-sickness risk. |

| Do not | Reason |
|---|---|
| Add a new hex or a gray/indigo utility to public UI | It breaks the fixed palette and the flat-vector system. |
| Use amber for normal text on latte or cream | `3.12:1` / `2.73:1` fails AA. |
| Hide a focusable control with opacity only | It stays in the tab order while invisible. |
| Mix Breeze gray/indigo primitives into public pages | They do not follow the coffee palette or type ramp. |

## Planned: flowing water redesign

Per `docs/ui_redesign_plan.md` (status 2026-11-02): Phase 1 foundations and Phase 2 home + bean detail are shipped. Not built yet:

- **Phase 3 — secondary pages:** recipes index/detail spec-card hero and timeline steps; methods; cheap with a normalized Rp/100g column; custom.
- **Phase 4 — motion and accessibility deep pass:** shared-element View Transition from card to detail hero, reveal cadence audit, feel-check of card lift, view-transition morph and chip press, full contrast and keyboard audit.
- **Deferred:** commerce, AJAX filtering, admin UI pass.
- **Brief-only, not built:** “cards have no hover effect.” The code currently ships a hover lift; no commit removes it.

## Cleanup needed

- **Card hover contradicts the brief.** `bean-card` and `method-card` lift, add an amber border, and add a shadow on hover; recipe cards change border/title. Decide whether the no-hover rule replaces this.
- **Card radius drift.** Cards are `16px`, cards’ media/quiz shells/result panels are `24px`, recipe spec cells are `4px` while other spec cells are `12px`, and Breeze uses `6px`/`8px`.
- **Legacy Breeze surface.** `primary-button`, `secondary-button`, `danger-button`, `text-input`, `modal`, `nav-link`, `responsive-nav-link`, and `dropdown` still use gray/indigo and `rounded-md`; the app and guest layouts load Figtree but `font-sans` asks for Inter, so they fall back to system fonts. They do not match the public system.
- **Spacing drift.** The stated rhythm is `88px`, but the hero uses `72px`, detail pages `48px`, cafes `24px/20px`, and custom `pt-16 pb-10`; arbitrary values bypass `section-top`.
- **Contrast comment is wrong.** `app.css` and `ui_redesign_plan.md` claim `amber-ink` is `6.3:1` on latte and `5.6:1` on cream; computed values are `5.08:1` and `4.45:1`. The cream pairing misses AA for normal text.
- **Raw hex repeats.** `#C87D55` appears on 8 lines (5 navbar active underlines, hero art, CSS), `#6F4E37` on 6, `#4A2C21` on 3, `#2C1A14` on 3, and `#FDFBF7` on 2. Replace with `var(--color-coffee-*)` or token utilities; recommended names: `--color-coffee-amber`, `--color-coffee-mocha`, `--color-coffee-roast`, `--color-coffee-espresso`, `--color-coffee-latte`.
- **White is outside the palette.** `bg-white`/`text-white` appear on 15 view lines (spec cells, equipment rows, sticky bar, recommended-drink chips, Breeze). Either approve `--color-coffee-paper: #FFFFFF` or replace with `coffee-latte`.
- **Touch targets below 44px.** Locale links are `36px`; recipe serving buttons are `40×40px`.
- **Hidden focus.** The Details button becomes `opacity-0` but stays tabbable, and hover-close can leave focus on the invisible overlay link.
- **SVG budget.** `application-logo.blade.php` is `3,045 B`, over the `2 KB` rule; the inline hero illustration also needs measurement.
