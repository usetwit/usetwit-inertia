<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonalProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updatePersonalProfile', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'personal_email' => 'nullable|email:strict|max:255',
            'first_name' => 'required|string|max:85',
            'middle_names' => 'nullable|string|max:85',
            'last_name' => 'nullable|string|max:85',
            'dob' => 'nullable|date_format:Y-m-d|after_or_equal:1900-01-01|before_or_equal:2050-12-31',
            'personal_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
            'personal_mobile_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
        ];
    }
}
