<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\GetUsersRequest;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumbs = Breadcrumbs::generate('admin.users.index');
        $heading = 'All Users';

        return Inertia::render('Admin/Users/Index', compact('heading', 'breadcrumbs'));
    }

    public function getUsers(GetUsersRequest $request, FilterService $service, GeneralSettings $settings)
    {
        $perPage = $request->integer('per_page', $settings->per_page_default);
        $filters = $request->array('filters');
        $sorts = $request->array('sort');
        $visible = $request->array('visible');

        $substitutions = ['role_name' => 'roles.name', 'id' => 'users.id'];
        $global = [
            'id',
            'username',
            'email',
            'first_name',
            'middle_names',
            'last_name',
            'full_name',
            'employee_id',
            'role_name',
        ];

        $cols = [
            'username',
            'email',
            'first_name',
            'middle_names',
            'last_name',
            'full_name',
            'employee_id',
            'joined_at',
            'created_at',
            'updated_at',
            'active',
            'slug',
            'id',
        ];

        $cols = array_map(fn ($value) => 'users.'.$value, $cols);
        $cols = array_merge($cols, ['roles.name as role_name']);

        $query = DB::table('users')->select($cols)->leftJoin('model_has_roles', function ($join) {
            $join->on('model_has_roles.model_id', 'users.id')->where('model_has_roles.model_type', User::class);
        })->leftJoin('roles', 'roles.id', 'model_has_roles.role_id');

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], $substitutions, $sorts);

        $paginator = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'email' => $item->email,
                'first_name' => $item->first_name,
                'middle_names' => $item->middle_names,
                'last_name' => $item->last_name,
                'full_name' => $item->full_name,
                'role_name' => $item->role_name,
                'employee_id' => $item->employee_id,
                'slug' => $item->slug,
                'active' => $item->active,
                'username' => $item->username,
                'joined_at' => $item->joined_at ? Carbon::parse($item->joined_at)->toDateString() : null,
                'created_at' => Carbon::parse($item->created_at)->toDateString(),
                'updated_at' => Carbon::parse($item->updated_at)->toDateString(),
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
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
