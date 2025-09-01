<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CalendarShifts\GetShiftsRequest;
use App\Models\Calendar;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class CalendarShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendar $calendar)
    {
        $name = $calendar->calendarable()->first()->name;
        $heading = 'Edit Calendar: '.$name;
        $breadcrumbs = Breadcrumbs::generate('admin.calendar-shifts.edit', $calendar);
        $shifts = $calendar->shifts()->get();

        return Inertia::render('Admin/CalendarShifts/Edit',
            compact('shifts', 'name', 'calendar', 'heading', 'breadcrumbs')
        );
    }

    public function getShifts(Calendar $calendar, GetShiftsRequest $request)
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

        return $return_array;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
