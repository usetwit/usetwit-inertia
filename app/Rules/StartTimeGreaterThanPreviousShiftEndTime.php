<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class StartTimeGreaterThanPreviousShiftEndTime implements ValidationRule
{
    public function __construct(protected string $endTime)
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
        $endTime = request()->input("dates.{$index}.{$this->endTime}");

        if($value <= $endTime || $endTime == '00:00') {
            $fail('Start times must be greater than previous shift end or 24 hours have already been specified.');
        }
    }
}
