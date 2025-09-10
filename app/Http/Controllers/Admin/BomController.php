<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Boms\GetBomsRequest;
use App\Http\Requests\Admin\Boms\GetVersionsRequest;
use App\Http\Requests\Admin\Boms\NameExistsRequest;
use App\Http\Requests\Admin\Boms\StoreRequest;
use App\Http\Requests\Admin\Boms\UpdateRequest;
use App\Models\Bom;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Exceptions\InvalidBreadcrumbException;
use Diglactic\Breadcrumbs\Exceptions\UnnamedRouteException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BomController extends Controller
{
    /**
     * @throws InvalidBreadcrumbException
     * @throws UnnamedRouteException
     */
    public function edit(Bom $bom)
    {
        $breadcrumbs = Breadcrumbs::generate('admin.boms.edit', $bom);
        $heading = 'Edit Bom: '.$bom->name;

        return Inertia::render('Admin/Boms/Edit', compact('bom', 'heading', 'breadcrumbs'));
    }

    public function update(Bom $bom, UpdateRequest $request): RedirectResponse
    {
        $bom->update($request->validated());

        return response()->redirectToRoute('admin.boms.edit', $bom)->with('success', 'BOM updated');
    }

    public function nameExists(NameExistsRequest $request): JsonResponse
    {
        $name = $request->string('name');

        $exists = Bom::withTrashed()->where('name', $name)->exists();

        return response()->json(compact('exists'));
    }

    public function index(): Response
    {
        $breadcrumbs = Breadcrumbs::generate('admin.boms.index');
        $heading = 'All BOMs';

        return Inertia::render('Admin/Boms/Index', compact('heading', 'breadcrumbs'));
    }

    public function create(): Response
    {
        $breadcrumbs = Breadcrumbs::generate('admin.boms.create');
        $heading = 'Create BOM';

        return Inertia::render('Admin/Boms/Create', compact('heading', 'breadcrumbs'));
    }

    public function store(StoreRequest $request)
    {
        return 'HI';
    }

    public function getBoms(GetBomsRequest $request, FilterService $service, GeneralSettings $settings): JsonResponse
    {
        $perPage = $request->integer('per_page', $settings->per_page_default);
        $filters = $request->array('filters');
        $sorts = $request->array('sort');
        $visible = $request->array('visible');

        $global = [
            'id',
            'name',
        ];

        $query = BOM::query()
            ->withTrashed();

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], sorts: $sorts);

        $paginator = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'name' => $item->name,
                'slug' => $item->slug,
                'active' => $item->active,
                'username' => $item->user?->username,
                'version' => $item->latestVersion?->version,
                'created_at' => $item->created_at->toDateString(),
                'updated_at' => $item->updated_at->toDateString(),
            ];
        });

        return response()->json([
            'items' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }

    public function getVersions(Bom $bom, GetVersionsRequest $request, FilterService $service, GeneralSettings $settings): JsonResponse
    {
        $perPage = $request->integer('per_page', $settings->per_page_default);
        $filters = $request->array('filters');
        $sorts = $request->array('sort');
        $visible = $request->array('visible');

        $global = [
            'version',
        ];

        $query = $bom->bomVersions();

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], sorts: $sorts);

        $paginator = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'version' => $item->version,
                'created_at' => $item->created_at->toDateString(),
                'updated_at' => $item->updated_at->toDateString(),
            ];
        });

        return response()->json([
            'items' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }
}
