<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'difficulty_level' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'description' => 'required|string|max:2000',
            'is_cheap' => 'boolean',
            'equipment_needed' => 'required|array|min:1',
            'equipment_needed.*' => 'string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }
}
