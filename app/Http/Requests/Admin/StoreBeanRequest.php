<?php

namespace App\Http\Requests\Admin;

use App\Rules\UniqueSlug;
use Illuminate\Foundation\Http\FormRequest;

class StoreBeanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = self::fieldRules();

        $rules['name'][] = new UniqueSlug('coffee_beans', $this->route('bean'));
        $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096';

        return $rules;
    }

    /**
     * Column rules shared by the admin form and the CSV importer, so both
     * paths validate the same fields the same way.
     */
    public static function fieldRules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:150'],
            'origin_country' => 'required|string|max:100',
            'acidity_level' => 'required|integer|between:1,5',
            'body_level' => 'required|integer|between:1,5',
            'price' => 'nullable|numeric|min:0|max:999999999',
            'price_unit' => 'nullable|string|max:30',
            'is_cheap' => 'boolean',
            'flavor_notes' => 'required|array|max:8',
            'flavor_notes.*' => 'string|max:40',
            'recommended_drinks' => 'nullable|array|max:6',
            'recommended_drinks.*' => 'string|max:60',
            'recommended_methods' => 'nullable|array|max:6',
            'recommended_methods.*' => 'string|exists:brew_methods,slug',
            'image_url' => 'nullable|string|max:255',
        ];
    }

    /**
     * The column is NOT NULL, but the form allows every drink row to be
     * removed. Normalise an omitted field to [] and drop blank rows (the
     * empty-string-to-null middleware would otherwise fail the string rule).
     */
    protected function prepareForValidation(): void
    {
        $drinks = $this->input('recommended_drinks');

        $this->merge([
            'recommended_drinks' => is_array($drinks)
                ? array_values(array_filter($drinks, fn ($drink) => is_string($drink) && trim($drink) !== ''))
                : ($drinks ?? []),
        ]);
    }
}
