<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ArrayUniqueValues implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            return;
        }

        if (count($value) !== count(array_unique($value))) {
            $fail('The array elements must be unique.');
        }
    }
}
