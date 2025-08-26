<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EndTimeGreaterThanStartTimeOrMidnight implements ValidationRule
{
    public function __construct(protected string $startTime)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $index = explode('.', $attribute)[1];
        if($value !== '00:00' && $value <= request()->input("dates.{$index}.{$this->startTime}")) {
            $fail('End time must be greater than start time for each shift or 00:00');
        }
    }
}
