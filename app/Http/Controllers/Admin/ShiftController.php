<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Boms\NameExistsRequest;
use App\Http\Requests\Admin\Shifts\GetItemsRequest;
use App\Http\Requests\Admin\Shifts\UpdateRequest;
use App\Models\Shift;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumbs = Breadcrumbs::generate('admin.shifts.index');
        $heading = 'All Shifts';

        return Inertia::render('Admin/Shifts/Index', compact('breadcrumbs', 'heading'));
    }

    public function getItems(GetItemsRequest $request, FilterService $service, GeneralSettings $settings): JsonResponse
    {
        $perPage = $request->integer('per_page', $settings->per_page_default);
        $filters = $request->array('filters');
        $sorts = $request->array('sort');
        $visible = $request->array('visible');

        $global = ['name'];

        $query = Shift::query();

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], sorts: $sorts);

        $paginator = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'active' => $item->active,
                'created_at' => $item->calendar->created_at->toDateString(),
                'updated_at' => $item->calendar->updated_at->toDateString(),
            ];
        });

        return response()->json([
            'items' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
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
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        $breadcrumbs = Breadcrumbs::generate('admin.shifts.edit', $shift);
        $heading = 'Edit Shift: '.$shift->name;

        $shift->load('calendar');

        //        dd($shift->calendar->id);

        return Inertia::render('Admin/Shifts/Edit', compact('shift', 'breadcrumbs', 'heading'));
    }

    public function nameExists(NameExistsRequest $request): JsonResponse
    {
        $exists = Shift::where('name', $request->input('name'))->exists();

        return response()->json(compact('exists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Shift $shift)
    {
        $shift->update($request->validated());

        return response()->redirectToRoute('admin.shifts.edit', $shift)->with('success', 'Shift updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        //
    }
}
