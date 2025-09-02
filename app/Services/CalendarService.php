<?php

namespace App\Services;

use App\Exceptions\EndLessThanOrEqualToStartException;
use App\Exceptions\HoursUsedException;
use App\Exceptions\IncorrectTimeFormatException;
use App\Exceptions\KeyMismatchException;
use App\Exceptions\StartLessThanOrEqualToPrevEndException;

class CalendarService
{
    /**
     * @throws EndLessThanOrEqualToStartException
     * @throws HoursUsedException
     * @throws KeyMismatchException
     * @throws IncorrectTimeFormatException
     * @throws StartLessThanOrEqualToPrevEndException
     */
    public function durations(array $times): array
    {
        $allowedKeys = [
            'shift1_start', 'shift1_end', 'shift2_start', 'shift2_end',
            'shift3_start', 'shift3_end', 'shift4_start', 'shift4_end',
        ];

        $keysToTest = collect(array_keys($times))->sort()->values();
        $allowedKeys = collect($allowedKeys)->sort()->values();

        if (! $keysToTest->diff($allowedKeys)->isEmpty() || ! $allowedKeys->diff($keysToTest)->isEmpty()) {
            throw new KeyMismatchException;
        }

        /*
         * Validate all the times
         */
        foreach ($times as $time) {
            if (! preg_match('#^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$#', $time) && $time !== null) {
                throw new IncorrectTimeFormatException($time);
            }
        }

        /*
         * Make sure only one end is at midnight
         */
        $ends = $times;
        array_filter($ends, function ($key) {
            return str_ends_with('end', $key);
        }, ARRAY_FILTER_USE_KEY);

        if (count(array_filter($ends, fn ($value) => $value === '00:00')) > 1) {
            throw new HoursUsedException;
        }

        /*
         * If end is midnight then add 24 hours
         */
        $timestamps = array_map('strtotime', $times);

        foreach ($timestamps as $key => &$value) {
            if (str_ends_with($key, 'end') && $times[$key] === '00:00') {
                $value += 24 * 60 * 60;
            }
        }

        $durations = ['total_duration' => 0];

        for ($i = 1; $i <= 4; $i++) {
            if (
                $timestamps["shift{$i}_end"] <= $timestamps["shift{$i}_start"]
                && $times["shift{$i}_end"] !== null
                && $times["shift{$i}_start"] !== null
            ) {
                throw new EndLessThanOrEqualToStartException("shift{$i}_end: ".$timestamps["shift{$i}_end"].", shift{$i}_start: ".$timestamps["shift{$i}_start"]);
            }

            if ($i > 1) {
                $prev = $i - 1;
                if ($timestamps["shift{$i}_start"] <= $timestamps["shift{$prev}_end"] && $times["shift{$i}_start"] !== null) {
                    throw new StartLessThanOrEqualToPrevEndException;
                }
            }

            $durations["shift{$i}_duration"] = ($timestamps["shift{$i}_end"] - $timestamps["shift{$i}_start"]) / 60;
            $durations['total_duration'] += $durations["shift{$i}_duration"];
        }

        return $durations;
    }
}
