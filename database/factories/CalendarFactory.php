<?php

namespace Database\Factories;

use App\Models\Calendar;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarFactory extends Factory
{
    protected $model = Calendar::class;

    public function definition(): array
    {
        return [
            'calendarable_id' => null,
            'calendarable_type' => null,
        ];
    }

    public function withCalendarable(): CalendarFactory
    {
        return $this->state(function () {
            $calendarableClass = collect(Calendar::$validCalendarables)->random();
            $calendarable = $calendarableClass::factory()->create();

            return [
                'calendarable_id' => $calendarable->id,
                'calendarable_type' => $calendarableClass,
            ];
        });
    }
}
