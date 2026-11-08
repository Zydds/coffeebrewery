<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CafeMapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bounds' => ['required', 'string', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'region' => ['sometimes', 'string', 'max:10'],
        ];
    }

    /**
     * The viewport's south, west, north, east edges as floats.
     *
     * @return array{0: float, 1: float, 2: float, 3: float}
     */
    public function bounds(): array
    {
        return array_map('floatval', explode(',', (string) $this->validated('bounds')));
    }
}
