<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Boms\CheckNameRequest;
use App\Http\Requests\Admin\Boms\GetBomsRequest;
use App\Http\Requests\Admin\Boms\UpdateRequest;
use App\Models\Bom;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class BomController extends Controller
{
    public function edit(Bom $bom)
    {
        $routes = [
            'update' => route('admin.boms.update', $bom),
            'check_name' => route('admin.boms.checkName'),
        ];

        $versions = $bom->bomVersions()
            ->orderByDesc('version')
            ->pluck('version', 'id')
            ->map(fn ($version) => "v{$version}");

        return view('admin.boms.edit', compact('bom', 'routes', 'versions'));
    }

    public function update(Bom $bom, UpdateRequest $request): JsonResponse
    {
        $bom->update($request->validated());

        return response()->json([
            'message' => 'BOM Updated Successfully',
        ]);
    }

    public function checkName(CheckNameRequest $request): JsonResponse
    {
        $exists = Bom::where('name', $request->input('name'))->exists();

        return response()->json(['exists' => $exists]);
    }

    public function index(GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();
        $paginationSettings = $settings->paginationSettings();

        $routes = [
            'get_boms' => route('admin.boms.get-boms'),
        ];

        $breadcrumbs = Breadcrumbs::generate('admin.boms.index');
        $heading = 'All Boms';

        return Inertia::render('Admin/Boms/Index', compact('dateSettings', 'paginationSettings', 'routes', 'heading', 'breadcrumbs'));
    }

    public function getBoms(GetBomsRequest $request, FilterService $service, GeneralSettings $settings): JsonResponse
    {
        $perPage = $request->integer('per_page', $settings->per_page_default);
        $filters = $request->input('filters', []);
        $sorts = $request->input('sort', []);
        $visible = $request->input('visible', []);

        $substitutions = ['id' => 'boms.id'];
        $global = [
            'id',
            'name',
        ];

        $query = BOM::query()
            ->with('latestVersion');

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], $substitutions, $sorts);

        $paginator = $query->paginate($perPage)->through(function ($bom) {
            return [
                'id' => $bom->id,
                'name' => $bom->name,
                'slug' => $bom->slug,
                'active' => $bom->active,
                'version' => $bom->latestVersion ? $bom->latestVersion->version : null,
                'edit_version_route' => $bom->latestVersion ? route('admin.bom-versions.edit', $bom->latestVersion) : null,
                'edit_bom_route' => route('admin.boms.edit', $bom->slug),
                'created_at' => $bom->created_at->toDateString(),
                'updated_at' => $bom->updated_at->toDateString(),
            ];
        });

        return response()->json([
            'boms' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }
}
