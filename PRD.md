# Coffeebrewery Product Requirements Document

Current state: code in this repository plus the status notes in `docs/`. Audience: a GitHub visitor, a new developer, or an AI agent with no prior context.

## 1. Overview

Coffeebrewery is a Laravel web app that explains coffee and shows how to brew it, aimed at people in Indonesia. It has five public pages (Home, Recipes, Cheap, Custom, Cafes), runs in English and Indonesian, and includes an admin CMS that is not linked from the public site. The code is Laravel 12 with Blade, Alpine.js, Tailwind 4, Vite, Leaflet, and Cloudinary; the domain used in the repo config is coffee.zaidanghiffari.my.id.

## 2. Problem and goals

Most coffee guides assume expensive equipment and imported beans. A visitor in Indonesia may have a sachet, a mug, and five minutes, and still be told to buy a grinder and a gooseneck kettle. Information about local beans, cheap methods, and nearby cafes is split across different sites.

Goals:

- Give a usable recipe quickly, with a filter for brews of 5 minutes or less.
- Cover cheap coffee: sachets, instant coffee, and low-cost brew methods.
- Explain beans and brew methods in plain language on the same site.
- Show cafes by province and city on a map.
- Let one admin edit all catalog content without touching code.
- Serve the public site in English and Indonesian.

## 3. Target users

**Student with a small budget.** Example: a student in Jakarta has Rp 10.000 and a few sachets at home. They open Cheap, filter to sachet coffee, and need a cold recipe that takes 5 minutes or less.

**New home brewer.** Example: someone in Bandung has just bought whole beans and a V60. They open Recipes, filter by V60, and need the ratio, water temperature, grind size, and steps for one cup.

**Visitor looking for a cafe.** Example: a remote worker in Jakarta Selatan opens Cafes, picks DKI Jakarta then Kota Jakarta Selatan, and needs a pin with opening hours and a directions link.

## 4. Core features

### 4.1 Home (`/`)

Purpose: the entry point. It shows the bean catalog, the brew method catalog, site totals, and a random recipe tool.

What the user can do:

- Read the hero with two actions: Surprise me and Quick recipes 5 min or less.
- See site-wide totals for beans, recipes, and methods, plus a link to the cafe map.
- Search beans by name or origin and filter by category, or search brew methods by name or description, then open a detail page.
- Roll a random recipe with optional Cheap only and Quick only toggles.

Acceptance criteria:

- [ ] `/` returns 200 and shows the bean catalog, brew methods, Surprise Me, site totals, and the cafe map link.
- [ ] The bean and method catalogs list regular items only (`is_cheap = false`), 8 per page, with search; beans also filter by category.
- [ ] The proof strip shows total counts, not the filtered page counts.
- [ ] Surprise Me returns a random recipe, supports cheap-only and 5-minutes-or-less filters, and links to the full recipe.
- [ ] `/beans/{slug}` shows origin, acidity and body, price, flavor notes, recommended drinks, and recipes that use the bean.
- [ ] `/methods/{slug}` shows description, difficulty, equipment, and recipes that use the method.
- [ ] An unknown bean or method slug returns 404.

### 4.2 Recipes (`/recipes`)

Purpose: browse and search every recipe, then open a full step-by-step guide.

What the user can do:

- Search by recipe title, bean name, or method name.
- Filter by All, Quick 5 min or less, Cheap, Featured, or a specific brew method, and page through 9 results at a time.
- Open a recipe for ratio, water temperature, grind size, time, steps, and linked bean and method.
- Change the serving count and see scaled coffee and water amounts when the ratio is written as `1:N`, or use `/recipes/random` for a random redirect.

Acceptance criteria:

- [ ] `/recipes` returns 200 and paginates at 9 recipes per page.
- [ ] Search is case-insensitive and matches recipe title, bean name, and method name.
- [ ] Quick filter returns only recipes with `brew_time_min` set and at most 5, ordered by brew time.
- [ ] Cheap, featured, and method filters combine with search and with each other.
- [ ] `/recipes/{slug}` returns 200 and shows the steps, metadata, linked bean and method, and schema.org Recipe JSON-LD.
- [ ] The servings control appears only when the ratio matches `1:N`, and multiplies both coffee grams and water millilitres.
- [ ] `/recipes/random` redirects to a recipe, or to `/recipes` when no recipe exists.
- [ ] An unknown recipe slug returns 404.

### 4.3 Cheap (`/cheap`)

Purpose: budget coffee in one place. The page is built around sachets, instant coffee, and cheap methods.

What the user can do:

- Browse sachet and instant beans, with search and category filter, and browse cheap brew methods with search.
- Roll a random cheap recipe. The Cheap toggle is omitted here because every roll is already cheap.

Acceptance criteria:

- [ ] `/cheap` returns 200 and lists only beans with `is_cheap = true`, 8 per page.
- [ ] Cheap methods list 8 per page.
- [ ] Category chips come only from categories that contain cheap beans.
- [ ] Bean search matches name or origin; method search matches name or description.
- [ ] The Surprise section on this page always requests cheap recipes and also supports the Quick only toggle.
- [ ] Empty results show a clear message and a way to clear search and filters.

### 4.4 Custom (`/custom`)

Purpose: a short quiz that suggests recipes the visitor can actually brew with what they have.

What the user can do:

- Answer five questions: what coffee they have, how much time they have, what gear they have, how they like it, and how they drink it.
- Skip the gear question when they choose sachet coffee, because gear is not relevant.
- See the best match with bean, method, ratio, temperature, grind, time, and a Cheap badge, then open the full recipe.
- Move to the next match, up to three, or start over.

Acceptance criteria:

- [ ] `/custom` returns 200 and shows the quiz.
- [ ] The quiz asks five questions in order and skips gear for sachet, for four questions in that path.
- [ ] Continue is disabled until the current question has an answer; Back is available after the first question.
- [ ] Submitting calls `/api/recipes/suggest` with the selected answers.
- [ ] The API validates each answer against the allowed values, scores every recipe, and returns the top 3.
- [ ] The result card shows the match details, a recipe link, Show another match only when more matches exist, and Start over.
- [ ] A failed request shows the error and a Try again action.
- [ ] The suggest API is limited to 30 requests per minute.

### 4.5 Cafes (`/cafes`)

Purpose: find cafes on a map by province and city, using OpenStreetMap data.

What the user can do:

- Pick a province, then a city or regency, and see cafe counts beside regions, including which regions have no data yet.
- Pan and zoom a clustered map; the map reloads cafes inside the current viewport.
- Tap a pin for the cafe name, area, opening hours, address, last OSM update date, and a Google Maps directions link.
- Share or reload the URL and return to the same region.

Acceptance criteria:

- [ ] `/cafes` returns 200 and renders the province and city selects on the server, with counts.
- [ ] The default region is `31.74` (Kota Jakarta Selatan), `?region=` selects another city, and the region is restored on load.
- [ ] The map shows clustered pins and reloads on pan and zoom without clearing all markers first.
- [ ] The map API accepts a bounding box and an optional region, returns visible cafes only, and caps results at 500.
- [ ] Popups show name, area, hours, address, last update date, and a directions link to the exact coordinates.
- [ ] Missing hours and addresses are omitted instead of printed as empty rows.
- [ ] The page includes OpenStreetMap and CARTO attribution.
- [ ] The map API is limited to 120 requests per minute.

### 4.6 Admin CMS (`/admin`, internal)

Purpose: let one admin keep the catalog current. It is not linked from the public site.

What the admin can do:

- Log in at `/login`. Public registration is disabled and `/register` returns 404.
- See counts for beans, recipes, methods, and categories on the dashboard, plus the five most recent beans and recipes.
- Create, edit, and delete categories, beans, brew methods, and recipes, with automatic slugs from the name or title.
- Upload or replace bean and method images through Cloudinary.

Acceptance criteria:

- [ ] A guest is redirected to `/login`, and a logged-in non-admin cannot reach `/admin`.
- [ ] An admin can open every admin list, create, and edit page for the four resources.
- [ ] Bean and method forms accept an image and save both the Cloudinary URL and public id.
- [ ] Deleting a bean with recipes, a method with recipes, or a category with beans is blocked with an error message.
- [ ] There is no admin screen for cafes and no admin screen for creating users.

## 5. Content model

What the site stores, in plain language.

| Item | What it holds | Notes |
|---|---|---|
| Category | A name, slug, and description | Groups beans. Seeded values: Arabica, Robusta, Liberica, Sachet, Instant. |
| Bean | Name, slug, category, origin country, acidity 1 to 5, body 1 to 5, optional price and price unit, cheap flag, flavor notes, recommended drinks, optional image | Price unit defaults to `100 gr`. Cheap beans appear on `/cheap`, regular beans on Home. |
| Brew method | Name, slug, difficulty (easy, medium, hard), description, cheap flag, equipment list, optional image | Regular methods appear on Home, cheap methods on `/cheap`. |
| Recipe | Title, slug, linked bean, linked method, water temperature, grind size, ratio text, optional brew time in minutes, ordered steps, featured flag, cheap flag | No image of its own. The servings scaler works only when the ratio is `1:N`. |
| Cafe | OSM id and type, name, latitude, longitude, region code, area name, opening hours text, street address parts, raw OSM tags, OSM timestamp, last seen date, missing flag | Imported from OpenStreetMap only. Rows are updated by import and never deleted; missing rows are flagged. |
| Wilayah | Official province and city/regency code, name, level, parent code | Levels 1 and 2 only. Used by the cafe region cascade. |
| Cafe area | OSM place node or curated alias, name, place type, coordinates, region code | Table and model exist. No public UI uses it yet; the in-map area dropdown is planned. |
| User | Name, email, password, admin flag | One seeded admin. No public registration. |

Relations: a category has many beans; a bean has many recipes; a brew method has many recipes; a recipe belongs to one bean and one method. The seeders create 5 categories, 14 beans, 7 brew methods, and 14 recipes for local setup and tests.

## 6. Non-goals

- No public user accounts, registration, favorites, ratings, reviews, or comments.
- No commerce: no cart, checkout, orders, payments, or shipping.
- No cafe owner claims, user-submitted cafe edits, admin cafe editing, or separate cafe detail pages. OpenStreetMap is the only source.
- No geolocation or distance sorting, and no live open or closed status. The region select is the only navigation input; hours are raw OSM text.
- No scheduled cafe imports. The import commands are run manually.
- No languages beyond English and Indonesian.
- No separate `/beans` or `/methods` index pages. The catalogs live on Home.

## 7. Success metrics

No analytics or event tracking is installed in the repo, so product targets cannot be confirmed. Quality targets below are enforced by code and tests.

| Metric | How it is measured | Target |
|---|---|---|
| Public pages available | Smoke test for `/`, `/recipes`, `/cheap`, `/custom`, `/cafes` | 5 of 5 return 200 |
| Automated test failures | `php artisan test` | 0 |
| Quick filter correctness | Recipes returned by `/recipes?quick=1` have `brew_time_min` at most 5 | 100% |
| Weekly recipe page views | Analytics, not installed | TBD |
| Quiz completion rate | Analytics, not installed | TBD |
| Cafe directions clicks | Analytics, not installed | TBD |
| Cafe rows refreshed in the last 90 days | Query on `cafes.last_seen_at` | TBD |

## 8. Roadmap

### Done

Evidence is the code in this repo. Docs status is noted where the code alone cannot show history.

- Public catalog: Home with bean and method catalogs, site totals, Surprise Me, plus bean and method detail pages.
- Recipe index with search, filters, and pagination; recipe detail with steps, servings scaler, and Recipe JSON-LD.
- Cheap page with sachet and instant beans, cheap methods, and a cheap-only Surprise section.
- Custom quiz with five questions, gear skip for sachet, and a top-3 suggestion API.
- Cafe map with province and city cascade, counts, default Jakarta Selatan, clustered Leaflet map, viewport API, URL sync, popup card with hours, address, update date, and directions, plus OpenStreetMap and CARTO attribution.
- Cafe data pipeline: `wilayah` and `cafes` tables, `wilayah:import` and `import:cafes` commands, upsert by OSM id and type, missing rows flagged and never deleted.
- English and Indonesian UI with a session-based locale switch. English is the default.
- Admin CMS: login, dashboard, CRUD for categories, beans, methods, and recipes, Cloudinary image upload, and delete guards.
- SEO basics: per-page titles and descriptions, canonical links, Open Graph tags, Recipe JSON-LD, and a robots file that allows all.
- Automated tests covering public pages, filtering, random redirect, SEO, locale, auth, admin pages, the quiz, and recipe scaling. The docs record 52 passing tests.
- UI redesign Phases 1 and 2, and cafe map Phases 1 and 2 plus the fix, UI, and evaluation rounds, per `docs/`.

### In progress

- None confirmed. No branch, pull request, or code marker in the repo identifies partially finished work. The docs name the next phases, but none is marked as started.

### Planned

From the status notes in `docs/`. Dates are TBD.

- UI Phases 3 and 4: secondary pages (recipes index and detail, methods, a price-per-100-gram column on Cheap, Custom), then a motion and accessibility deep pass.
- Cafe Phase 3 remainder: opening-hours parser for the four formats found in the audit, and a draggable mobile bottom sheet. The popup card, hours text, address, and directions link already ship.
- Cafe Phases 4 and 5: in-map area dropdown limited to areas that contain cafes, plus curated aliases, then the i18n sweep, `/cafes` SEO and Open Graph tags, import and cafe tests, and browser verification. `config/cafe_area_aliases.php` does not exist yet.
- More cafe regions beyond the pilot. The current default is `31.74` Jakarta Selatan. The region list and coverage target are TBD.

## 9. Open questions

- Where does production run, and is the site live now? The repo has no deploy config. Deployment platform and status are TBD. Which analytics tool and events will measure the metrics? Analytics is TBD.
- Who owns content updates for recipes, beans, and methods, and how often will they happen? Content owner and cadence are TBD.
- Should the admin CMS get a public link, for example in the footer? Admin link is TBD. Is one admin account enough, or is a user management screen needed? Additional admin accounts are TBD.
- Should Indonesian be the default language for the main audience, or stay a switch from English? Default language is TBD.
- The Custom page meta description says four questions, but the page asks five, or four when sachet is chosen. Which wording is correct?
- Which cafe regions must be live before the cafe map is considered shipped, and how fresh must the data be? Region coverage is TBD.

### Consolidated TBD list

1. Weekly recipe page views target.
2. Quiz completion rate target.
3. Cafe directions clicks target.
4. Cafe data freshness target.
5. Roadmap dates.
6. Cafe region list and coverage target.
7. Deployment platform, production status, and analytics tool.
8. Content owner and update cadence.
9. Whether the admin CMS gets a public link, and whether more admin accounts are needed.
10. Default language choice.
