<?php

namespace App\Http\Middleware;

use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        $items = [
            [
                'label' => 'Locations',
                'icon' => 'pi pi-map-marker',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All Locations',
                        'route' => 'locations.index',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Location',
                        'route' => 'locations.create',
                    ],
                ],
            ],
            [
                'label' => 'Settings',
                'icon' => 'pi pi-cog',
                'links' => [
                    [
                        'type' => 'heading',
                        'label' => 'Settings',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Company Settings',
                        'route' => 'company.edit',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Application Settings',
                        'route' => 'application.index',
                    ],
                ],
            ],
            [
                'label' => 'Calendars',
                'icon' => 'pi pi-calendar',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All Calendars',
                        'route' => 'calendars.index',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Calendar',
                        'route' => 'calendars.create',
                    ],
                ],
            ],
            [
                'label' => 'Users',
                'icon' => 'pi pi-users',
                'links' => [
                    [
                        'type' => 'heading',
                        'label' => 'Users',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'All Users',
                        'route' => 'users.index',
                        'matches' => [
                            'users.edit',
                        ],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create User',
                        'route' => 'users.create',
                    ],
                ],
            ],
            [
                'label' => 'Sales Orders',
                'icon' => 'pi pi-dollar',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All Sales Orders',
                        'route' => 'sales-orders.index',
                        'matches' => [
                            'sales-orders.edit',
                        ],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Sales Order',
                        'route' => 'sales-orders.create',
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
                        'route' => 'boms.index',
                        'matches' => [
                            'boms.edit',
                        ],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create BOM',
                        'route' => 'boms.create',
                    ],
                ],
            ],
        ];

        $current = Route::currentRouteName();

        foreach ($items as $i => &$item) {
            $item['expanded'] = false;
            $item['id'] = $i;

            foreach ($item['links'] as $j => &$link) {
                $link['id'] = "{$i}-{$j}";

                if ($link['type'] !== 'link') {
                    continue;
                }

                $link['link'] = route("admin.{$link['route']}");

                $matches = $link['matches'] ?? [];
                $matches[] = $link['route'];
                $prefixedMatches = array_map(fn ($m) => "admin.$m", $matches);

                $link['active'] = in_array($current, $prefixedMatches, true);

                if (! $item['expanded'] && $link['active']) {
                    $item['expanded'] = true;
                }

                unset($link['matches'], $link['route']);
            }
        }

        return $items;
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
        $user = Auth::user();
        $userData = null;
        $settings = app(GeneralSettings::class);

        if ($user) {
            $filename = $user->profileImages()->default()->value('filename');

            $userData = [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'profile_image' => $filename
                    ? asset("images/user/profile/{$user->id}/{$filename}", true)
                    : null,
            ];
        }

        return array_merge(parent::share($request), [
            'app_name' => config('app.name'),
            'user' => $userData,
            'default_profile_image' => asset('images/user/profile/profile_default.svg', true),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'version' => config('app.version'),
            'sidebar_items' => $this->sidebarItems(),
            'logo' => asset('images/logo.svg', true),
            'dateSettings' => $settings->dateSettings(),
            'paginationSettings' => $settings->paginationSettings(),
        ]);
    }
}
