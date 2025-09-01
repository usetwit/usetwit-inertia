<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CalendarShifts\GetShiftsRequest;
use App\Http\Requests\Admin\CalendarShifts\UpdateRequest;
use App\Models\Calendar;
use App\Models\CalendarShift;
use App\Services\CalendarService;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class CalendarShiftsController extends Controller
{
    public function __construct(private readonly CalendarService $calendarService) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendar $calendar)
    {
        $name = $calendar->calendarable()->first()->name;
        $heading = 'Edit Calendar: '.$name;
        $breadcrumbs = Breadcrumbs::generate('admin.calendar-shifts.edit', $calendar);
        $shifts = $calendar->shifts()->get();

        return Inertia::render('Admin/CalendarShifts/Edit', compact('shifts', 'name', 'calendar', 'heading', 'breadcrumbs'));
    }

    public function getShifts(Calendar $calendar, GetShiftsRequest $request): JsonResponse
    {
        $shifts = $calendar->shifts()
            ->whereYear('shift_date', $request->input('year'))
            ->get([
                'shift_date',
                'shift1_start',
                'shift1_end',
                'shift2_start',
                'shift2_end',
                'shift3_start',
                'shift3_end',
                'shift4_start',
                'shift4_end',
                'nwd',
            ]);

        $return_array = [];

        foreach ($shifts as $shift) {
            $return_array[$shift->shift_date->format('Y-m-d')] = Arr::except($shift, 'shift_date');
        }

        return response()->json(['shifts' => $return_array]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Calendar $calendar, UpdateRequest $request): JsonResponse
    {
        $dates = $request->input('dates');
        $year = $request->input('year');
        $shifts = [];
        $base = ['calendar_id' => $calendar->id];

        CalendarShift::whereYear('shift_date', $year)
            ->where('calendar_id', $calendar->id)
            ->delete();

        foreach ($dates as $date) {

            $shift_date = ['shift_date' => $date['shift_date']];

            if ($date['nwd'] === true) {
                $shifts[] = $base + $shift_date + [
                    'nwd' => true,
                    'shift1_start' => '00:00',
                    'shift1_end' => '00:00',
                    'shift2_start' => null,
                    'shift2_end' => null,
                    'shift3_start' => null,
                    'shift3_end' => null,
                    'shift4_start' => null,
                    'shift4_end' => null,
                    'total_duration' => 0,
                    'shift1_duration' => 0,
                    'shift2_duration' => 0,
                    'shift3_duration' => 0,
                    'shift4_duration' => 0,
                ];

            } elseif ($date['shift1_end'] !== '00:00') {

                $times = Arr::only($date, [
                    'shift1_start',
                    'shift1_end',
                    'shift2_start',
                    'shift2_end',
                    'shift3_start',
                    'shift3_end',
                    'shift4_start',
                    'shift4_end',
                ]);

                $shifts[] = $base + $times + $shift_date + $this->calendarService->durations($times) + [
                    'nwd' => false,
                ];
            }
        }

        CalendarShift::insert($shifts);

        return response()->json(['success' => 'Shifts Updated']);
    }
}
