<?php

namespace App\Http\Requests\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProtectedInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateProtectedInfo', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'joined_at' => 'nullable|date_format:Y-m-d|after_or_equal:2024-01-01|before_or_equal:2050-12-31',
            'left_at' => 'nullable|date_format:Y-m-d|after_or_equal:2024-01-01|before_or_equal:2050-12-31',
            'role_id' => 'required|integer|exists:roles,id',
        ];
    }
}
