<?php

namespace App\Services;

use App\Http\Requests\Admin\StoreBeanRequest;
use App\Http\Requests\Admin\StoreRecipeRequest;
use App\Models\BrewMethod;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Reads a catalog CSV twice: once to preview it, once to write it after the
 * admin confirms. Both passes run the same parsing and validation, and both
 * reuse the admin form rules, so a preview cannot disagree with the commit.
 */
class CatalogImporter
{
    public const MAX_ROWS = 500;

    public const TYPES = [
        'beans' => [
            'table' => 'coffee_beans',
            'model' => CoffeeBean::class,
            'headers' => ['slug', 'name', 'category_slug', 'origin_country', 'acidity_level', 'body_level', 'price', 'price_unit', 'is_cheap', 'flavor_notes', 'recommended_drinks', 'recommended_methods', 'image_url'],
            'required' => ['name', 'category_slug', 'origin_country', 'acidity_level', 'body_level', 'flavor_notes'],
            'example' => ['sidama-guji', 'Sidama Guji', 'arabica', 'Ethiopia', '4', '3', '95000', '100 gr', '0', 'Citrus|Floral', 'Espresso|Latte', 'v60-pour-over|aeropress', ''],
        ],
        'recipes' => [
            'table' => 'recipes',
            'model' => Recipe::class,
            'headers' => ['slug', 'title', 'bean_slug', 'method_slug', 'water_temp_c', 'grind_size', 'ratio', 'brew_time_min', 'step_by_step', 'is_featured', 'cheap', 'image_url'],
            'required' => ['title', 'bean_slug', 'method_slug', 'water_temp_c', 'grind_size', 'ratio', 'step_by_step'],
            'example' => ['gayo-aceh-v60', 'Gayo Aceh V60', 'gayo-aceh', 'v60-pour-over', '93', 'medium-fine', '1:15', '4', 'Grind the beans|Rinse the filter|Pour in circles', '0', '0', ''],
        ],
    ];

    /**
     * Header row and example row for the downloadable template.
     *
     * @return array{0: list<string>, 1: list<string>}
     */
    public function template(string $type): array
    {
        return [self::TYPES[$type]['headers'], self::TYPES[$type]['example']];
    }

    /**
     * Parse and validate without writing anything.
     *
     * @return array{rows: list<array<string, mixed>>, valid: int, new: int, updates: int, errors: int}
     */
    public function preview(string $type, string $path): array
    {
        $parsed = $this->parse($path, $type);
        $rows = $this->prepare($type, $parsed, $this->existingSlugs($type, $parsed));

        $new = count(array_filter($rows, fn (array $row) => $row['status'] === 'new'));
        $updates = count(array_filter($rows, fn (array $row) => $row['status'] === 'update'));

        return [
            'rows' => $rows,
            'valid' => $new + $updates,
            'new' => $new,
            'updates' => $updates,
            'errors' => count($rows) - $new - $updates,
        ];
    }

    /**
     * Write the valid rows inside one transaction. Rows that fail validation
     * are returned instead of written.
     *
     * @return array{created: int, updated: int, skipped: int, failed: list<array{line: int, key: string, reason: string}>}
     */
    public function commit(string $type, string $path): array
    {
        $rows = $this->prepare($type, $this->parse($path, $type));
        $model = self::TYPES[$type]['model'];
        $failed = [];
        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($model, $rows, &$failed, &$created, &$updated) {
            foreach ($rows as $row) {
                if ($row['status'] === 'error') {
                    $failed[] = ['line' => $row['line'], 'key' => $row['key'], 'reason' => $row['reason']];

                    continue;
                }

                $payload = $row['payload'];
                unset($payload['slug']);

                $record = $model::updateOrCreate(['slug' => $row['payload']['slug']], $payload);

                $record->wasRecentlyCreated ? $created++ : $updated++;
            }
        });

        return ['created' => $created, 'updated' => $updated, 'skipped' => count($failed), 'failed' => $failed];
    }

    /**
     * @return array<string, mixed>
     */
    private function parse(string $path, string $type): array
    {
        $contents = file_get_contents($path);

        if ($contents === false || trim($contents) === '') {
            throw ValidationException::withMessages(['file' => 'The file is empty.']);
        }

        if (! mb_check_encoding($contents, 'UTF-8')) {
            throw ValidationException::withMessages(['file' => 'The file must be UTF-8 encoded.']);
        }

        if (str_starts_with($contents, "\xEF\xBB\xBF")) {
            $contents = substr($contents, 3);
        }

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $contents);
        rewind($handle);

        $lines = [];

        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $lines[] = $row;
        }

        fclose($handle);

        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($lines) ?? []);

        if (count($headers) === 1 && str_contains($headers[0], ';')) {
            throw ValidationException::withMessages(['file' => 'Use commas as the delimiter, not semicolons.']);
        }

        $missing = array_diff(self::TYPES[$type]['required'], $headers);

        if ($missing !== []) {
            throw ValidationException::withMessages(['file' => 'Missing columns: '.implode(', ', $missing).'.']);
        }

        $rows = [];

        foreach ($lines as $index => $line) {
            if (array_filter($line, fn ($cell) => trim((string) $cell) !== '') === []) {
                continue;
            }

            if (count($line) > count($headers)) {
                $rows[] = ['line' => $index + 2, 'cells' => null, 'reason' => 'The row has more columns than the header.'];

                continue;
            }

            $cells = [];

            foreach ($headers as $position => $header) {
                $value = trim((string) ($line[$position] ?? ''));
                $cells[$header] = $value === '' ? null : $value;
            }

            foreach (self::TYPES[$type]['headers'] as $header) {
                $cells[$header] ??= null;
            }

            $rows[] = ['line' => $index + 2, 'cells' => $cells];
        }

        if ($rows === []) {
            throw ValidationException::withMessages(['file' => 'The file has no data rows.']);
        }

        if (count($rows) > self::MAX_ROWS) {
            throw ValidationException::withMessages(['file' => 'A file can hold at most '.self::MAX_ROWS.' rows.']);
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @param  array<string, int>  $existing
     * @return list<array<string, mixed>>
     */
    private function prepare(string $type, array $parsed, array $existing = []): array
    {
        $categories = $type === 'beans' ? Category::pluck('id', 'slug')->all() : [];
        $beans = $type === 'recipes' ? CoffeeBean::pluck('id', 'slug')->all() : [];
        $methods = BrewMethod::pluck('id', 'slug')->all();
        $seen = [];
        $rows = [];

        foreach ($parsed['rows'] as $row) {
            $rows[] = $type === 'beans'
                ? $this->prepareBean($row, $categories, $existing, $seen)
                : $this->prepareRecipe($row, $beans, $methods, $existing, $seen);
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, int>  $categories
     * @param  array<string, int>  $existing
     * @param  array<string, true>  $seen
     * @return array<string, mixed>
     */
    private function prepareBean(array $row, array $categories, array $existing, array &$seen): array
    {
        if ($row['cells'] === null) {
            return $this->failure($row['line'], '', $row['reason']);
        }

        $cells = $row['cells'];
        $slug = $cells['slug'] ?? Str::slug((string) $cells['name']);
        $reason = $this->slugProblem($slug);

        if ($reason === null && $cells['category_slug'] === null) {
            $reason = 'category_slug is required.';
        }

        if ($reason === null && ! isset($categories[$cells['category_slug']])) {
            $reason = "Category {$cells['category_slug']} does not exist.";
        }

        if ($reason !== null) {
            return $this->failure($row['line'], $slug, $reason);
        }

        $candidate = $this->withoutBlanks([
            'category_id' => $categories[$cells['category_slug']],
            'name' => $cells['name'],
            'origin_country' => $cells['origin_country'],
            'acidity_level' => $cells['acidity_level'],
            'body_level' => $cells['body_level'],
            'price' => $cells['price'],
            'price_unit' => $cells['price_unit'] ?? '100 gr',
            'is_cheap' => $this->flag($cells['is_cheap']),
            'flavor_notes' => $this->list($cells['flavor_notes']),
            'recommended_drinks' => $this->list($cells['recommended_drinks']),
            'recommended_methods' => $this->list($cells['recommended_methods']),
            'image_url' => $cells['image_url'],
        ]);

        return $this->validated($row['line'], $slug, $candidate, StoreBeanRequest::fieldRules(), $existing, $seen);
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, int>  $beans
     * @param  array<string, int>  $methods
     * @param  array<string, int>  $existing
     * @param  array<string, true>  $seen
     * @return array<string, mixed>
     */
    private function prepareRecipe(array $row, array $beans, array $methods, array $existing, array &$seen): array
    {
        if ($row['cells'] === null) {
            return $this->failure($row['line'], '', $row['reason']);
        }

        $cells = $row['cells'];
        $slug = $cells['slug'] ?? Str::slug((string) $cells['title']);
        $reason = $this->slugProblem($slug);

        if ($reason === null && $cells['bean_slug'] === null) {
            $reason = 'bean_slug is required.';
        }

        if ($reason === null && ! isset($beans[$cells['bean_slug']])) {
            $reason = "Bean {$cells['bean_slug']} does not exist.";
        }

        if ($reason === null && $cells['method_slug'] === null) {
            $reason = 'method_slug is required.';
        }

        if ($reason === null && ! isset($methods[$cells['method_slug']])) {
            $reason = "Method {$cells['method_slug']} does not exist.";
        }

        if ($reason !== null) {
            return $this->failure($row['line'], $slug, $reason);
        }

        $candidate = $this->withoutBlanks([
            'coffee_bean_id' => $beans[$cells['bean_slug']],
            'brew_method_id' => $methods[$cells['method_slug']],
            'title' => $cells['title'],
            'water_temp_c' => $cells['water_temp_c'],
            'grind_size' => $cells['grind_size'],
            'ratio' => $cells['ratio'],
            'brew_time_min' => $cells['brew_time_min'],
            'step_by_step' => $this->list($cells['step_by_step']),
            'is_featured' => $this->flag($cells['is_featured']),
            'cheap' => $this->flag($cells['cheap']),
            'image_url' => $cells['image_url'],
        ]);

        return $this->validated($row['line'], $slug, $candidate, StoreRecipeRequest::fieldRules(), $existing, $seen);
    }

    /**
     * Run the shared form rules and keep the validated payload.
     *
     * @param  array<string, mixed>  $candidate
     * @param  array<string, mixed>  $rules
     * @param  array<string, int>  $existing
     * @param  array<string, true>  $seen
     * @return array<string, mixed>
     */
    private function validated(int $line, string $slug, array $candidate, array $rules, array $existing, array &$seen): array
    {
        $validator = Validator::make($candidate, $rules);

        if ($validator->fails()) {
            return $this->failure($line, $slug, implode(' ', array_slice($validator->errors()->all(), 0, 2)));
        }

        if (isset($seen[$slug])) {
            return $this->failure($line, $slug, 'Duplicate slug in this file.');
        }

        $seen[$slug] = true;
        $payload = $validator->validated();
        $payload['slug'] = $slug;

        return [
            'line' => $line,
            'key' => $slug,
            'status' => isset($existing[$slug]) ? 'update' : 'new',
            'reason' => null,
            'payload' => $payload,
        ];
    }

    /**
     * A blank optional cell leaves the column alone rather than clearing it,
     * so re-importing a template never wipes an uploaded image or a price.
     *
     * @param  array<string, mixed>  $cells
     * @return array<string, mixed>
     */
    private function withoutBlanks(array $cells): array
    {
        return array_filter($cells, fn ($value) => $value !== null);
    }

    private function slugProblem(string $slug): ?string
    {
        if ($slug === '') {
            return 'The name or slug is required.';
        }

        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)
            ? null
            : "Slug {$slug} may only use lowercase letters, numbers and hyphens.";
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function failure(int $line, string $key, ?string $reason): array
    {
        return ['line' => $line, 'key' => $key, 'status' => 'error', 'reason' => $reason, 'payload' => null];
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @return array<string, int>
     */
    private function existingSlugs(string $type, array $parsed): array
    {
        $key = $type === 'beans' ? 'name' : 'title';
        $slugs = [];

        foreach ($parsed['rows'] as $row) {
            if ($row['cells'] === null) {
                continue;
            }

            $slug = $row['cells']['slug'] ?? Str::slug((string) $row['cells'][$key]);

            if ($slug !== '') {
                $slugs[$slug] = true;
            }
        }

        if ($slugs === []) {
            return [];
        }

        return array_flip(
            DB::table(self::TYPES[$type]['table'])->whereIn('slug', array_keys($slugs))->pluck('slug')->all()
        );
    }

    private function flag(?string $value): int
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'yes'], true) ? 1 : 0;
    }

    /**
     * @return list<string>
     */
    private function list(?string $value): array
    {
        if ($value === null) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode('|', $value)), fn ($item) => $item !== ''));
    }
}
