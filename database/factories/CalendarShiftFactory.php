<?php

namespace Database\Factories;

use App\Exceptions\EndLessThanOrEqualToStartException;
use App\Exceptions\HoursUsedException;
use App\Exceptions\IncorrectTimeFormatException;
use App\Exceptions\KeyMismatchException;
use App\Exceptions\StartLessThanOrEqualToPrevEndException;
use App\Models\Calendar;
use App\Models\CalendarShift;
use App\Services\CalendarService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CalendarShiftFactory extends Factory
{
    protected $model = CalendarShift::class;

    /**
     * @throws EndLessThanOrEqualToStartException
     * @throws HoursUsedException
     * @throws KeyMismatchException
     * @throws IncorrectTimeFormatException
     * @throws StartLessThanOrEqualToPrevEndException
     */
    public function definition(): array
    {
        $calendarService = app(CalendarService::class);

        $times = $this->generateValidShiftTimes();
        $durations = $calendarService->durations($times);
        $shiftDate = $this->faker->dateTimeBetween('2020-01-01', '2050-12-31');

        return array_merge([
            'calendar_id' => Calendar::factory()->withCalendarable(),
            'nwd' => false,
            'shift_date' => $shiftDate,
        ], $times, $durations);
    }

    private function generateValidShiftTimes(): array
    {
        $times = [];
        $shifts = ['1', '2', '3', '4'];
        $previousEnd = Carbon::createFromFormat('H:i', '00:00');

        foreach ($shifts as $shift) {
            $start = $previousEnd->copy()->addMinutes(rand(30, 180));
            $end = $start->copy()->addMinutes(rand(60, 180));

            $times["shift{$shift}_start"] = $start->format('H:i');
            $times["shift{$shift}_end"] = $end->format('H:i');

            $previousEnd = $end;
        }

        return $times;
    }
}
