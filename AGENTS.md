# AGENTS.md

Rules for any AI agent working in this repository.

## 1. Read first

- Read `PRD.md` for scope, acceptance criteria, and non-goals before building a feature.
- Read `ARCHITECTURE.md` for the stack, folder map, data model, and known risks before changing structure.
- Read `DESIGN_SYSTEM.md` for palette, type, components, motion, and accessibility before touching UI.
- Read `docs/ui_redesign_plan.md` before UI work; phases 3 and 4 are still pending.

## 2. Commands

- Install: `composer install` then `npm install`.
- Dev server: run `php artisan serve` in one terminal and `npm run dev` in another.
- `composer dev` exists but fails on Windows because Pail requires the `pcntl` extension.
- Build: `npm run build`.
- Test: `composer test`, which clears config then runs PHPUnit.
- Format: `php vendor/bin/pint`.
- Lint: `php vendor/bin/pint --test`.
- Migrate: `php artisan migrate`; run `php artisan migrate --pretend` first because local `.env` points at shared Postgres.

## 3. Working style

- Ask before adding a dependency, changing the database schema, or touching more than 5 files.
- Make the smallest change that solves the task; do no unrelated refactors.
- Write a numbered plan before any task that touches more than one file, then execute it.
- Run format, lint, and tests before declaring done; report every failure and its file.
- Never say something works without running the command that proves it.
- Read the files you will edit before editing them; do not guess at APIs, values, or names.

## 4. Code quality

- Comment only the reason for a non-obvious decision; delete comments that restate the code.
- Add no abstraction until a second caller exists; no one-use helpers; no "just in case" config.
- Delete dead code, unused JS imports, and commented-out blocks.
- Keep each function to one job and each file under 300 lines; split by responsibility.
- Use the domain names already in the repo: bean, method, recipe, cafe, wilayah, roast, latte.
- Handle errors at boundaries such as Form Requests and external API clients; no blanket try/catch.

## 5. Laravel rules

- Keep controllers thin; move validation into Form Requests under `app/Http/Requests/`.
- Move repeated query constraints into Eloquent scopes on the model.
- Eager load relations with `with()` when a view reads them; do not create N+1 queries.
- Keep Blade to display and loops; no queries, writes, or business rules in views.
- Read settings through `config()`; add new settings to `config/*.php` and `.env.example`.
- Never commit secrets or real credentials; `.env` stays untracked.

## 6. Frontend rules

- Reuse a component in `resources/views/components/` before creating a new one.
- Use only `coffee-*` tokens and documented Tailwind values; no hex codes, gray, indigo, or `bg-white` in public UI.
- Use no inline `style` attributes; put one-off values in `app.css`.
- Use Alpine only for local UI state; do not add another JS framework.
- Add no new animation library; use the existing anime.js helpers in `resources/js/app.js`.
- Animate `transform` and `opacity` only; give every animation a `prefers-reduced-motion` fallback.
- Keep one looping animation per page.

## 7. Content and i18n

- Wrap every user-facing string in `__()`, including titles, placeholders, and `aria-label` values.
- Add the Indonesian translation to `lang/id.json` in the same change; English is the key.
- Add framework auth and password messages to `lang/id/auth.php` and `lang/id/passwords.php`.
- Keep stored content in English; do not add per-locale columns without approval.

## 8. Git

- Make one logical change per commit.
- Use `type(scope): imperative summary`, with type from `feat`, `fix`, `docs`, `refactor`, `test`, or `chore`.
- Never commit `.env`, credentials, or `public/build/`.
- Rebase or merge from `main` instead of rewriting shared history.

## 9. Never do

Get explicit approval before:

- Deleting or rewriting an existing migration.
- Running `git push --force` or rewriting published history.
- Adding, removing, or changing a palette token.
- Linking `/admin` from a public page or exposing an admin route outside `auth` plus `admin`.
- Adding analytics, tracking pixels, or third-party embeds.
- Adding a dependency or changing the database schema.

## Definition of done

1. `git diff` shows only files the task needs.
2. `php vendor/bin/pint` has run on changed PHP files.
3. `php vendor/bin/pint --test` passes, or every failure is named in the report.
4. `composer test` passes, or every failure is named in the report.
5. `npm run build` passes when CSS, JS, or Blade changed.
6. The changed page or command was run and its output was checked.
