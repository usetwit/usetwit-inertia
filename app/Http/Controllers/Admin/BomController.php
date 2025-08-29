<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Boms\GetBomsRequest;
use App\Http\Requests\Admin\Boms\GetVersionsRequest;
use App\Http\Requests\Admin\Boms\NameExistsRequest;
use App\Http\Requests\Admin\Boms\StoreRequest;
use App\Http\Requests\Admin\Boms\UpdateRequest;
use App\Models\Bom;
use App\Models\BomVersion;
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
        $exists = Bom::where('name', $request->input('name'))->exists();

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

        $substitutions = ['id' => 'boms.id'];
        $global = [
            'id',
            'name',
        ];

        $query = BOM::query()
            ->withTrashed();

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], $substitutions, $sorts);

        $paginator = $query->paginate($perPage)->through(function ($bom) {
            return [
                'id' => $bom->id,
                'user_id' => $bom->user_id,
                'name' => $bom->name,
                'slug' => $bom->slug,
                'active' => $bom->active,
                'username' => $bom->user?->username,
                'version' => $bom->latestVersion?->version,
                'created_at' => $bom->created_at->toDateString(),
                'updated_at' => $bom->updated_at->toDateString(),
            ];
        });

        return response()->json([
            'boms' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }

    public function getVersions(GetVersionsRequest $request, FilterService $service, GeneralSettings $settings): JsonResponse
    {
        $perPage = $request->integer('per_page', $settings->per_page_default);
        $filters = $request->array('filters');
        $sorts = $request->array('sort');
        $visible = $request->array('visible');

        $global = [
            'version',
        ];

        $query = BomVersion::query();

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], sorts: $sorts);

        $paginator = $query->paginate($perPage)->through(function ($version) {
            return [
                'id' => $version->id,
                'version' => $version->version,
                'created_at' => $version->created_at->toDateString(),
                'updated_at' => $version->updated_at->toDateString(),
            ];
        });

        return response()->json([
            'boms' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }
}
