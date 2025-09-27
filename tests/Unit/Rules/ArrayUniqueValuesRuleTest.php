<?php

namespace Rules;

use App\Rules\ArrayUniqueValues;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ArrayUniqueValuesRuleTest extends TestCase
{
    private function validate(array $data)
    {
        return Validator::make($data, [
            'values' => [new ArrayUniqueValues],
        ]);
    }

    public function test_fails_if_array_contains_duplicate_values()
    {
        $validator = $this->validate(['values' => [1, 2, 3, 2]]);

        $this->assertTrue($validator->fails());
        $this->assertEquals(['The array elements must be unique.'], $validator->errors()->get('values'));
    }

    public function test_passes_if_array_contains_unique_values()
    {
        $validator = $this->validate(['values' => [1, 2, 3, 4]]);

        $this->assertFalse($validator->fails());
    }

    public function test_passes_if_array_is_empty()
    {
        $validator = $this->validate(['values' => []]);

        $this->assertFalse($validator->fails());
    }

    public function test_ignores_non_array_values()
    {
        $validator = $this->validate(['values' => 'not-an-array']);

        $this->assertFalse($validator->fails());
    }

    public function test_passes_if_array_contains_case_sensitive_unique_values()
    {
        $validator = $this->validate(['values' => ['TEST', 'test', 'Test']]);

        $this->assertFalse($validator->fails());
    }

    public function test_fails_if_array_contains_exact_duplicate_strings()
    {
        $validator = $this->validate(['values' => ['test', 'test', 'TEST']]);

        $this->assertTrue($validator->fails());
    }

    public function test_passes_if_array_contains_numbers_and_string_versions_of_numbers()
    {
        $validator = $this->validate(['values' => [1, '1', 2, '2']]);

        $this->assertTrue($validator->fails());
    }
}
