<?php

namespace Services;

use App\Exceptions\EndLessThanOrEqualToStartException;
use App\Exceptions\HoursUsedException;
use App\Exceptions\IncorrectTimeFormatException;
use App\Exceptions\KeyMismatchException;
use App\Exceptions\StartLessThanOrEqualToPrevEndException;
use App\Services\CalendarService;
use Tests\TestCase;

class CalendarServiceTest extends TestCase
{
    private CalendarService $calendarService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calendarService = new CalendarService;
    }

    public function test_valid_durations()
    {
        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '12:00',
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
            'shift3_end' => '22:00',
            'shift4_start' => '23:00',
            'shift4_end' => '00:00',
        ];

        $expectedDurations = [
            'total_duration' => 780,
            'shift1_duration' => 240,
            'shift2_duration' => 240,
            'shift3_duration' => 240,
            'shift4_duration' => 60,
        ];

        $result = $this->calendarService->durations($times);

        $this->assertEquals($expectedDurations, $result);
    }

    public function test_missing_time_throws_exception()
    {
        $this->expectException(KeyMismatchException::class);

        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '12:00',
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
        ];

        $this->calendarService->durations($times);
    }

    public function test_end_less_than_start()
    {
        $this->expectException(EndLessThanOrEqualToStartException::class);

        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '07:00', // End before start
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
            'shift3_end' => '22:00',
            'shift4_start' => '23:00',
            'shift4_end' => '00:00',
        ];

        $this->calendarService->durations($times);
    }

    public function test_values_can_be_null()
    {
        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '09:00',
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
            'shift3_end' => '20:00',
            'shift4_start' => null,
            'shift4_end' => null,
        ];

        $expectedDurations = [
            'total_duration' => 420,
            'shift1_duration' => 60,
            'shift2_duration' => 240,
            'shift3_duration' => 120,
            'shift4_duration' => 0,
        ];

        $result = $this->calendarService->durations($times);

        $this->assertEquals($expectedDurations, $result);
    }

    /**
     * @throws IncorrectTimeFormatException
     * @throws StartLessThanOrEqualToPrevEndException
     * @throws KeyMismatchException
     * @throws HoursUsedException
     */
    public function test_end_equal_to_start()
    {
        $this->expectException(EndLessThanOrEqualToStartException::class);

        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '08:00', // End equal to start
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
            'shift3_end' => '22:00',
            'shift4_start' => '23:00',
            'shift4_end' => '00:00',
        ];

        $this->calendarService->durations($times);
    }

    public function test_incorrect_time_format_throws_exception()
    {
        $this->expectException(IncorrectTimeFormatException::class);

        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '12:00',
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
            'shift3_end' => '22:00',
            'shift4_start' => '23:00',
            'shift4_end' => '24:00',
        ];

        $this->calendarService->durations($times);
    }

    public function test_24hours_used_exception()
    {
        $this->expectException(HoursUsedException::class);

        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '12:00',
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
            'shift3_end' => '00:00',
            'shift4_start' => '23:00',
            'shift4_end' => '00:00',
        ];

        $this->calendarService->durations($times);
    }

    public function test_extraneous_keys_found_exception()
    {
        $this->expectException(KeyMismatchException::class);

        $times = [
            'shift1_start' => '08:00',
            'shift1_end' => '12:00',
            'shift2_start' => '13:00',
            'shift2_end' => '17:00',
            'shift3_start' => '18:00',
            'shift3_end' => '22:00',
            'shift4_start' => '23:00',
            'shift4_end' => '00:00',
            'extra_end' => '10:00',
        ];

        $this->calendarService->durations($times);
    }
}
