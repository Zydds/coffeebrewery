<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Validates the slug that will be derived from the submitted name/title,
 * while reporting the error on that visible field instead of a hidden slug.
 */
class UniqueSlug implements ValidationRule
{
    public function __construct(
        private string $table,
        private ?Model $ignore = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table($this->table)->where('slug', Str::slug((string) $value));

        if ($this->ignore) {
            $query->where('id', '!=', $this->ignore->getKey());
        }

        if ($query->exists()) {
            $fail(__('That name is already in use.'));
        }
    }
}
