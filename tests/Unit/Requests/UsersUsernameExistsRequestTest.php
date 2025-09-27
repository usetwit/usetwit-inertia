<?php

namespace Requests;

use App\Http\Requests\Admin\Users\UsernameExistsRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UsersUsernameExistsRequestTest extends TestCase
{
    private function validate(array $data)
    {
        $request = new UsernameExistsRequest;

        return Validator::make($data, $request->rules());
    }

    public function test_username_is_required()
    {
        $validator = $this->validate([]);
        $this->assertTrue($validator->errors()->has('username'));
    }

    public function test_username_cannot_be_null()
    {
        $validator = $this->validate(['username' => null]);
        $this->assertTrue($validator->errors()->has('username'));
    }

    public function test_username_must_be_a_string()
    {
        $validator = $this->validate(['username' => 12345]);
        $this->assertTrue($validator->errors()->has('username'));
    }

    public function test_username_can_be_a_valid_string()
    {
        $validator = $this->validate(['username' => 'validusername']);
        $this->assertFalse($validator->errors()->has('username'));
    }

    public function test_username_maximum_length_is_255_characters()
    {
        $username = $this->longString();
        $validator = $this->validate(['username' => $username]);
        $this->assertTrue($validator->errors()->has('username'));
    }

    public function test_username_is_valid_within_max_length()
    {
        $username = $this->longString(85);
        $validator = $this->validate(['username' => $username]);
        $this->assertFalse($validator->errors()->has('username'));
    }

    public function test_username_can_be_numeric_string()
    {
        $validator = $this->validate(['username' => '12345']);
        $this->assertFalse($validator->errors()->has('username'));
    }
}
