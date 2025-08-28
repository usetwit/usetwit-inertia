<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleAdminInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'admin/layout';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    private function sidebarItems(): array
    {
        return [
            [
                'label' => 'Sales Orders',
                'icon' => 'pi pi-dollar',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All Sales Orders',
                        'route' => 'admin.sales-orders.index',
                        'matches' => [
                            'admin.sales-orders.edit',
                        ],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Sales Order',
                        'route' => 'admin.sales-orders.create',
                    ],
                ],
            ],
            [
                'label' => 'BOMs',
                'icon' => 'pi pi-sitemap',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All BOMs',
                        'route' => 'admin.boms.index',
                        'matches' => [
                            'admin.boms.edit',
                        ],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create BOM',
                        'route' => 'admin.boms.create',
                    ],
                ],
            ],
        ];
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if ($request->expectsJson()) {
            return parent::share($request);
        }

        $user = Auth::user();
        $settings = app(GeneralSettings::class);

        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'user' => $user,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'profileImage' => $this->profileImage($user),
            'version' => config('app.version'),
            'sidebar_items' => $this->sidebarItems(),
            'logo' => asset('images/logo.svg', true),
            'dateSettings' => $settings->dateSettings(),
            'paginationSettings' => $settings->paginationSettings(),
            'permissions' => $user?->getAllPermissions()->pluck('name'),
        ]);
    }

    private function profileImage(?User $user): string
    {
        if (! $user) {
            return '';
        }

        $filename = $user->profileImages()->default()?->value('filename');

        if (! $filename) {
            return asset('images/user/profile/profile_default.svg', true);
        }

        return asset("images/user/profile/{$user->id}/{$filename}", true);
    }
}
