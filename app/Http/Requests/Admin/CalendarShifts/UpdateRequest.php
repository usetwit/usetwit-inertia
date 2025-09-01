<?php

namespace App\Http\Requests\Admin\CalendarShifts;

use App\Rules\EndTimeGreaterThanStartTimeOrMidnight;
use App\Rules\StartTimeGreaterThanPreviousShiftEndTime;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit', $this->route('calendar'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'dates' => 'required|array|min:1',
            'year' => 'required|integer|between:2020,2050',
            'dates.*.shift_date' => 'required|date_format:Y-m-d',
            'dates.*.nwd' => 'required|boolean',
            'dates.*.shift1_start' => [
                'required_with:dates.*.shift1_end,dates.*.shift2_start',
                'required_if_declined:dates.*.nwd',
                'date_format:H:i',
            ],
            'dates.*.shift1_end' => [
                'required_with:dates.*.shift1_start',
                'required_if_declined:dates.*.nwd',
                'date_format:H:i',
                new EndTimeGreaterThanStartTimeOrMidnight('shift1_start'),
            ],
            'dates.*.shift2_start' => [
                'nullable',
                'required_with:dates.*.shift2_end,dates.*.shift3_start',
                new StartTimeGreaterThanPreviousShiftEndTime('shift1_end'),
            ],
            'dates.*.shift2_end' => [
                'nullable',
                'required_with:dates.*.shift2_start',
                'date_format:H:i',
                new EndTimeGreaterThanStartTimeOrMidnight('shift2_start'),
            ],
            'dates.*.shift3_start' => [
                'nullable',
                'required_with:dates.*.shift3_end,dates.*.shift4_start',
                new StartTimeGreaterThanPreviousShiftEndTime('shift2_end'),
            ],
            'dates.*.shift3_end' => [
                'nullable',
                'required_with:dates.*.shift3_start',
                'date_format:H:i',
                new EndTimeGreaterThanStartTimeOrMidnight('shift3_start'),
            ],
            'dates.*.shift4_start' => [
                'nullable',
                'required_with:dates.*.shift4_end',
                new StartTimeGreaterThanPreviousShiftEndTime('shift3_end'),
            ],
            'dates.*.shift4_end' => [
                'nullable',
                'required_with:dates.*.shift4_start',
                'date_format:H:i',
                new EndTimeGreaterThanStartTimeOrMidnight('shift4_start'),
            ],
        ];
    }
}
