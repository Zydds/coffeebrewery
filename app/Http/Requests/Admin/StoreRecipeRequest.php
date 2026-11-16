<?php

namespace App\Http\Requests\Admin;

use App\Rules\UniqueSlug;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = self::fieldRules();

        $rules['title'][] = new UniqueSlug('recipes', $this->route('recipe'));
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
            'coffee_bean_id' => 'required|exists:coffee_beans,id',
            'brew_method_id' => 'required|exists:brew_methods,id',
            'title' => ['required', 'string', 'max:200'],
            'water_temp_c' => 'required|integer|between:60,100',
            'grind_size' => 'required|string|max:50',
            'ratio' => 'required|string|max:50',
            'brew_time_min' => 'nullable|integer|between:1,120',
            'step_by_step' => 'required|array|min:1',
            'step_by_step.*' => 'string|max:500',
            'is_featured' => 'boolean',
            'cheap' => 'boolean',
            'image_url' => 'nullable|string|max:255',
        ];
    }
}
