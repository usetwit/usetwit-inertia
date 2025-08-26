<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Postcode implements ValidationRule
{
    public function __construct(
        protected int $maxLength = 12,
    )
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value)) {
            $fail("The {$attribute} must be a string.");
            return;
        }

        $length = mb_strlen($value);
        if ($length > $this->maxLength) {
            $fail("The {$attribute} may not be greater than {$this->maxLength} characters.");
            return;
        }

        $pattern = '/^[A-Za-z0-9](?:[A-Za-z0-9\-\s]*[A-Za-z0-9])?$/';
        if (! preg_match($pattern, $value)) {
            $fail("The {$attribute} must be a valid postal code (letters, numbers, spaces or hyphens; no leading/trailing space).");
        }
    }
}
