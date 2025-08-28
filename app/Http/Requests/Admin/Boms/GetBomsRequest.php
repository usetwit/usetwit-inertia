<?php

namespace App\Http\Requests\Admin\Boms;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Models\Bom;
use App\Models\User;
use App\Services\FilterService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetBomsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', Bom::class);
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
                'name',
            ],
            'date' => [
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

        return $service->makeValidationRules($filterRules, ['version', 'username']);
    }
}
