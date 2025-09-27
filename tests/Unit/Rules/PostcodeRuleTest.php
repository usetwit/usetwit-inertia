<?php

namespace Rules;

use App\Rules\Postcode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Tests\TestCase;

class PostcodeRuleTest extends TestCase
{
    /**
     * Helper method to validate a postcode using Postcode rule.
     */
    protected function validatePostcode(mixed $postcode): Validator
    {
        return ValidatorFacade::make(
            ['postcode' => $postcode],
            ['postcode' => [new Postcode]]
        );
    }

    public function test_valid_postcode_passes()
    {
        $validator = $this->validatePostcode('AB12 3CD');
        $this->assertFalse($validator->fails(), 'The validator should pass for a valid postcode.');
    }

    public function test_null_value_passes()
    {
        $validator = $this->validatePostcode(null);
        $this->assertFalse($validator->fails(), 'The validator should pass for null values.');
    }

    public function test_empty_string_passes()
    {
        $validator = $this->validatePostcode('');
        $this->assertFalse($validator->fails(), 'The validator should pass for empty strings.');
    }

    public function test_non_string_fails()
    {
        $validator = $this->validatePostcode(12345);
        $this->assertTrue($validator->fails(), 'The validator should fail for non-string values.');
        $this->assertStringContainsString('must be a string', $validator->errors()->first('postcode'));
    }

    public function test_too_long_postcode_fails()
    {
        $tooLong = str_repeat('A', 13);
        $validator = $this->validatePostcode($tooLong);
        $this->assertTrue($validator->fails(), 'The validator should fail for a postcode longer than maxLength.');
        $this->assertStringContainsString('may not be greater than', $validator->errors()->first('postcode'));
    }

    public function test_invalid_characters_fails()
    {
        $validator = $this->validatePostcode('ABC@123');
        $this->assertTrue($validator->fails(), 'The validator should fail for postcodes with invalid characters.');
        $this->assertStringContainsString('must be a valid postal code', $validator->errors()->first('postcode'));
    }

    public function test_leading_space_fails()
    {
        $validator = $this->validatePostcode(' AB12');
        $this->assertTrue($validator->fails(), 'The validator should fail for postcodes with leading space.');
        $this->assertStringContainsString('must be a valid postal code', $validator->errors()->first('postcode'));
    }

    public function test_trailing_space_fails()
    {
        $validator = $this->validatePostcode('AB12 ');
        $this->assertTrue($validator->fails(), 'The validator should fail for postcodes with trailing space.');
        $this->assertStringContainsString('must be a valid postal code', $validator->errors()->first('postcode'));
    }

    public function test_hyphenated_postcode_passes()
    {
        $validator = $this->validatePostcode('AB-12-3CD');
        $this->assertFalse($validator->fails(), 'The validator should pass for valid hyphenated postcodes.');
    }
}
