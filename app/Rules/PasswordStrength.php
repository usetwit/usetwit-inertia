<?php

namespace App\Rules;

use App\Settings\GeneralSettings;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use ZxcvbnPhp\Zxcvbn;

class PasswordStrength implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $zxcvbn = new Zxcvbn;
        $settings = app(GeneralSettings::class);

        if ($zxcvbn->passwordStrength(substr($value, 0, 150))['score'] < $settings->password_strength) {
            $fail('The :attribute field is not a strong enough password.');
        }
    }
}
