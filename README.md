# Coffeebrewery

Coffeebrewery is a bilingual coffee education and brewing guide for people in Indonesia. It helps visitors understand coffee beans, find practical brewing recipes, choose affordable options, discover brew methods, and locate nearby cafes.

Live site: [coffeebrewery.vercel.app](https://coffeebrewery.vercel.app)

## What it includes

- Bean catalog with origin, flavor profile, acidity, body, price, and recommended drinks
- Bean detail pages with origin maps and related recipes
- Recipe browsing with search and filters for quick, cheap, featured, and brew-method recipes
- Recipe detail pages with ratios, water temperature, grind size, steps, and serving scaling
- Cheap coffee section for sachets, instant coffee, and budget brewing
- Custom brew finder that recommends recipes based on coffee, time, equipment, taste, and drink preferences
- Surprise Me random recipe picker
- Cafe map with region filters, clustered markers, opening hours, addresses, and directions links
- English and Indonesian localization
- Admin CMS for managing categories, beans, brew methods, recipes, cafes, images, and CSV imports

## Product direction

Coffeebrewery focuses on practical coffee education rather than commerce or social networking. The central question is:

> What coffee can this person make well with the ingredients, equipment, time, and budget they already have?

Public accounts, comments, ratings, favorites, payments, and user-generated posts are intentionally out of scope for the current product.

## Tech stack

- Laravel 12
- PHP 8.2+
- PostgreSQL in production through Neon
- SQLite is used by the automated test suite
- Blade templates
- Alpine.js
- Tailwind CSS 4
- Vite
- Leaflet and Leaflet MarkerCluster for cafe maps
- Cloudinary for uploaded media
- PHPUnit for tests
- Vercel with `vercel-php` for deployment

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- PostgreSQL for a local PostgreSQL setup, or SQLite for basic local development
- Cloudinary credentials only if testing image uploads

## Local setup

Clone the repository and install dependencies:

```bash
git clone https://github.com/Zydds/coffeebrewery.git
cd coffeebrewery
composer install
npm install
```

Create the local environment file:

```bash
copy .env.example .env
php artisan key:generate
```

For the default SQLite setup, create the database file if it does not exist:

```bash
New-Item database/database.sqlite -ItemType File -Force
php artisan migrate
php artisan db:seed
```

Start the application and Vite in separate terminals:

```bash
php artisan serve
npm run dev
```

Open [http://localhost:8000](http://localhost:8000).

The seeded local admin credentials come from `ADMIN_EMAIL` and `ADMIN_PASSWORD`. Set them in `.env` before running the seeder. Do not use fallback credentials in a shared or production environment.

## Useful commands

```bash
# Build frontend assets
npm run build

# Run the test suite
composer test

# Check PHP formatting
php vendor/bin/pint --test

# Apply PHP formatting
php vendor/bin/pint

# Inspect routes
php artisan route:list

# Check migration status
php artisan migrate:status
```

## Database and content imports

Cafe and wilayah imports are maintenance commands and should be run locally or from a controlled deployment environment, not as part of the Vercel build:

```bash
php artisan import:cafes
php artisan wilayah:import
```

The cafe importer keeps manually created cafes separate from OpenStreetMap records. Manual rows use `source=manual`; imported rows use `source=osm`.

Catalog CSV imports are available from the admin CMS. The importer validates rows in a preview step before committing changes.

## Admin CMS

The admin area is intentionally not linked from the public navigation.

- Login: `/login`
- Dashboard: `/admin`
- Catalog import: `/admin/import`
- Cafe management: `/admin/cafes`

Admin access is protected by authentication and the `is_admin` user flag. Create separate accounts for collaborators instead of sharing a password. Disable a collaborator by setting `is_admin` to `false`.

## Production deployment

The application is configured for Vercel through:

- `api/index.php` — Laravel PHP entrypoint
- `vercel.json` — PHP function, static asset build, routing, and Singapore function region

Set production values in Vercel Environment Variables. Never commit `.env`, credentials, or generated `public/build` assets.

Typical production values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://coffeebrewery.vercel.app
APP_KEY=base64:...

DB_CONNECTION=pgsql
DB_URL=postgresql://user:password@host/database?sslmode=require

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
CLOUDINARY_URL=cloudinary://...
```

Use a PostgreSQL connection compatible with the Vercel PHP runtime. Neon connections may require the endpoint configuration used by `config/database.php`.

Vercel has no queue worker in this setup, so `QUEUE_CONNECTION=sync` is required. Run migrations, seeders, and data imports manually against the production database; do not add them to the Vercel build command.

## Architecture overview

```text
Browser
  → Vercel PHP function
    → Laravel routes, controllers, requests, Blade views
      → Neon PostgreSQL
      → Cloudinary

Browser
  → Leaflet map tiles and cafe markers
  → Cloudinary image URLs
  → Google Maps directions links
```

Important application areas:

- `app/Http/Controllers` — public, API, admin, auth, and profile controllers
- `app/Models` — beans, recipes, brew methods, cafes, regions, and users
- `app/Services` — catalog writing, CSV importing, cafe importing, recipe matching, and Cloudinary uploads
- `app/Support` — server-side page data builders and SEO helpers
- `resources/views` — Blade layouts, public pages, components, and admin screens
- `resources/js` — Surprise Me, custom brew finder, cafe map, bean map, and page interactions
- `resources/css` — design tokens, responsive layout, motion, and accessibility rules
- `database/migrations` — schema history
- `tests` — feature and unit coverage

## Content and media rules

- Store content in English in the database; translations belong in `lang/id.json`.
- Wrap user-facing UI text in Laravel translation helpers.
- Store uploaded media in Cloudinary rather than the ephemeral Vercel filesystem.
- Keep external media URLs allowlisted and validated before embedding them.
- Never commit secrets, real passwords, `.env`, or production database URLs.

## Testing

The test suite covers catalog reading, recipe matching, cafe imports and map responses, admin writes, CSV imports, authentication, localization, SEO, and public page rendering.

Run:

```bash
composer test
```

Before opening a pull request:

```bash
php vendor/bin/pint --test
composer test
npm run build
git diff --check
```

## Contributing

1. Create a focused branch.
2. Read `PRD.md`, `ARCHITECTURE.md`, and `DESIGN_SYSTEM.md` before changing behavior or UI.
3. Keep changes small and domain-focused.
4. Add or update tests for non-trivial behavior.
5. Run formatting, tests, and the production asset build.
6. Do not commit secrets or generated production assets.

## License

This project is licensed under the MIT License.
