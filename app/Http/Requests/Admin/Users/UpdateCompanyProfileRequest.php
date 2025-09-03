<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateCompanyProfile', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'company_ext' => 'nullable|string|regex:/^[0-9 ]*$/|max:255',
            'company_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
            'company_mobile_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
            'email' => 'nullable|email:strict|max:255',
        ];
    }
}
