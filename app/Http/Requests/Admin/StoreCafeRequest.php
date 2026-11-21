<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCafeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public static function fieldRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'region_kode' => ['required', 'string', 'max:10'],
            'area_name' => ['nullable', 'string', 'max:150'],
            'opening_hours' => ['nullable', 'string', 'max:500'],
            'addr_street' => ['nullable', 'string', 'max:150'],
            'addr_housenumber' => ['nullable', 'string', 'max:30'],
            'addr_city' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'array'],
        ];
    }

    public function rules(): array
    {
        return self::fieldRules();
    }
}
