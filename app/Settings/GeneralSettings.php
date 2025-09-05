<?php

namespace App\Settings;

use Illuminate\Support\Collection;
use Spatie\LaravelSettings\Settings;
use Symfony\Component\Intl\Countries;

class GeneralSettings extends Settings
{
    public string $locale;

    public array $locales;

    public string $currency;

    public array $currencies;

    public int $employee_id_padding;

    public string $employee_id_prefix;

    public string $default_country;

    public int $per_page_default;

    public array $per_page_options;

    public array $date_validation_separators;

    public array $date_validation_regex;

    public string $date_validation_separator_default;

    public string $date_validation_default;

    public int $password_strength;

    public array $colors;

    public static function group(): string
    {
        return 'general';
    }

    /**
     * Replace hyphens with default separator
     */
    public function dateFormatForDisplay(): string
    {
        $format = str_replace('-', $this->date_validation_separator_default, $this->date_validation_default);

        return strtolower($format);
    }

    public function dateFormats(): array
    {
        return array_keys(array_change_key_case($this->date_validation_regex));
    }

    public function dateRegexDefault(): string
    {
        return $this->date_validation_regex[$this->date_validation_default];
    }

    public function dateSettings(): array
    {
        return [
            'regex' => $this->dateRegexDefault(),
            'display' => $this->dateFormatForDisplay(),
            'separator' => $this->date_validation_separator_default,
            'format' => $this->date_validation_default,
        ];
    }

    public function paginationSettings(): array
    {
        return [
            'per_page' => [
                'default' => $this->per_page_default,
                'options' => $this->per_page_options,
            ],
        ];
    }

    public function countries(): Collection
    {
        return collect(Countries::getNames())
            ->map(fn ($name, $code) => compact('name', 'code'))
            ->values();
    }
}
