<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class HasMultipleConstraints implements ValidationRule
{
    public function __construct(protected ?array $constraints)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($this->constraints) && count($this->constraints) > 1 && empty($value)) {
            $fail('The :attribute field is required when there is more than one constraint.');
        }
    }
}
