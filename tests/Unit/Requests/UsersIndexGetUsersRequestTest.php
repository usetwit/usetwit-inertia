<?php

namespace Requests;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Http\Requests\Admin\Users\GetUsersRequest;
use App\Rules\HasMultipleConstraints;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class UsersIndexGetUsersRequestTest extends TestCase
{
    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    private function validate(array $data)
    {
        $filterService = app(FilterService::class);

        $settings = $this->mock(GeneralSettings::class);
        $settings->per_page_options = [10, 25, 50];

        $request = new GetUsersRequest;

        return Validator::make($data, $request->rules($filterService));
    }

    public function test_operator_is_required_with_multiple_constraints()
    {
        $constraints = [
            ['value' => 'john_doe', 'mode' => 'contains'],
            ['value' => 'jane_smith', 'mode' => 'contains'],
        ];

        $errorMessage = '';
        $rule = new HasMultipleConstraints($constraints);
        $fail = $this->failClosure($errorMessage);

        $rule->validate('filters.username.operator', '', $fail);

        $this->assertEquals('The :attribute field is required when there is more than one constraint.', $errorMessage);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_max_5_constraints_permitted()
    {
        $data = [
            'filters' => [
                'first_name' => [
                    'constraints' => [
                        ['value' => 'john_doe', 'mode' => 'contains'],
                        ['value' => 'jane_smith', 'mode' => 'contains'],
                        ['value' => 'jane_smith', 'mode' => 'contains'],
                        ['value' => 'jane_smith', 'mode' => 'contains'],
                        ['value' => 'jane_smith', 'mode' => 'contains'],
                        ['value' => 'jane_smith', 'mode' => 'contains'],
                    ],
                ],
            ],
        ];

        $validator = $this->validate($data);

        $this->assertTrue($validator->errors()
            ->first() === 'The filters.first name.constraints field must not have more than 5 items.');
    }

    public function test_operator_is_not_required_with_single_constraints()
    {
        $constraints = [
            ['value' => 'john_doe', 'mode' => 'contains'],
        ];

        $errorMessage = '';
        $rule = new HasMultipleConstraints($constraints);
        $fail = $this->failClosure($errorMessage);

        $rule->validate('filters.username.operator', '', $fail);

        $this->assertTrue($errorMessage === '');
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_per_page_must_be_integer()
    {
        $validator = $this->validate(['per_page' => 'string_value']);

        $this->assertTrue($validator->errors()->has('per_page'));
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_per_page_must_be_in_allowed_options()
    {
        $validator = $this->validate(['per_page' => 101]);

        $this->assertTrue($validator->errors()->has('per_page'));
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_valid_per_page_value_is_accepted()
    {
        $validator = $this->validate(['per_page' => 100]);

        $this->assertFalse($validator->errors()->has('per_page'));
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_match_mode_must_be_provided_when_filter_value_is_set()
    {
        $data = [
            'filters' => [
                'username' => [
                    'constraints' => [
                        ['value' => 'john_doe'],
                    ],
                ],
            ],
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->errors()->has('filters.username.constraints.0.mode'));
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_visible_field_is_valid()
    {
        $data = ['visible' => ['username']];
        $validator = $this->validate($data);

        $this->assertFalse($validator->fails());
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_invalid_visible_field()
    {
        $data = ['visible' => ['invalid_field']];
        $validator = $this->validate($data);

        $this->assertTrue($validator->errors()->has('visible.*'));
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_sort_field_and_order_are_valid()
    {
        $data = [
            'sort' => [
                [
                    'field' => 'username',
                    'order' => 'asc',
                ],
            ],
        ];

        $validator = $this->validate($data);
        $this->assertFalse($validator->fails());
    }

    /**
     * @throws FilterServiceGetTypeInvalidException|Exception
     */
    public function test_invalid_sort_order_triggers_error()
    {
        $data = [
            'sort' => [
                [
                    'field' => 'username',
                    'order' => 'invalid_sort',
                ],
            ],
        ];

        $validator = $this->validate($data);
        $this->assertTrue($validator->errors()->has('sort.0.order'));
    }
}
