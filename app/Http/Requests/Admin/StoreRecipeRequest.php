<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coffee_bean_id' => 'required|exists:coffee_beans,id',
            'brew_method_id' => 'required|exists:brew_methods,id',
            'title' => 'required|string|max:200',
            'water_temp_c' => 'required|integer|between:60,100',
            'grind_size' => 'required|string|max:50',
            'ratio' => 'required|string|max:50',
            'step_by_step' => 'required|array|min:1',
            'step_by_step.*' => 'string|max:500',
            'is_featured' => 'boolean',
        ];
    }
}
