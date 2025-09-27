<?php

namespace Requests;

use App\Http\Requests\Admin\CalendarShifts\UpdateRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CalendarShiftsUpdateRequestTest extends TestCase
{
    private function validate(array $data)
    {
        $request = new UpdateRequest;

        return Validator::make($data, $request->rules());
    }

    public function test_year_is_required()
    {
        $validator = $this->validate([]);
        $this->assertTrue($validator->errors()->has('year'));
    }

    public function test_year_must_be_integer()
    {
        $validator = $this->validate(['year' => 'twenty-twenty-three']);
        $this->assertTrue($validator->errors()->has('year'));
    }

    public function test_year_must_be_at_least_2020()
    {
        $validator = $this->validate(['year' => 2019]);
        $this->assertTrue($validator->errors()->has('year'));
    }

    public function test_year_must_not_exceed_2050()
    {
        $validator = $this->validate(['year' => 2051]);
        $this->assertTrue($validator->errors()->has('year'));
    }

    public function test_year_is_valid_when_within_range()
    {
        $validator = $this->validate(['year' => 2025]);
        $this->assertFalse($validator->errors()->has('year'));
    }

    public function test_year_accepts_minimum_value()
    {
        $validator = $this->validate(['year' => 2020]);
        $this->assertFalse($validator->errors()->has('year'));
    }

    public function test_year_accepts_maximum_value()
    {
        $validator = $this->validate(['year' => 2050]);
        $this->assertFalse($validator->errors()->has('year'));
    }
}
