<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'origin_country' => 'required|string|max:100',
            'acidity_level' => 'required|integer|between:1,5',
            'body_level' => 'required|integer|between:1,5',
            'flavor_notes' => 'required|array|max:8',
            'flavor_notes.*' => 'string|max:40',
            'recommended_drinks' => 'nullable|array|max:6',
            'recommended_drinks.*' => 'string|max:60',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }
}
