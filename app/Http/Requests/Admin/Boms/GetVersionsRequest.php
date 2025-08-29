<?php

namespace App\Http\Requests\Admin\Boms;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Models\BomVersion;
use App\Services\FilterService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetVersionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', BomVersion::class);
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
            'number' => [
                'version',
            ],
        ];

        return $service->makeValidationRules($filterRules);
    }
}
