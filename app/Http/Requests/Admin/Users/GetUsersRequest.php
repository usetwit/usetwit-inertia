<?php

namespace App\Http\Requests\Admin\Users;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Models\User;
use App\Services\FilterService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     *
     * @throws FilterServiceGetTypeInvalidException
     */
    public function rules(FilterService $service): array
    {
        $filterRules = [
            'string' => [
                'username',
                'email',
                'first_name',
                'middle_names',
                'last_name',
                'full_name',
                'employee_id',
                'role_name',
                'global',
            ],
            'date' => [
                'joined_at',
                'created_at',
                'updated_at',
            ],
            'boolean' => [
                'active',
            ],
            'number' => [
                'id',
            ],
        ];

        return $service->makeValidationRules($filterRules);
    }
}
