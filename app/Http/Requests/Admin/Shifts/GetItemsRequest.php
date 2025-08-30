<?php

namespace App\Http\Requests\Admin\Shifts;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Services\FilterService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('shifts.edit') || $this->user()->can('shifts.view');
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
            'date' => [
                'created_at',
                'updated_at',
            ],
            'string' => [
                'name',
            ],
            'boolean' => [
                'active',
            ],
        ];

        return $service->makeValidationRules($filterRules);
    }
}
