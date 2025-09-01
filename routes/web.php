<?php

use App\Http\Middleware\HandleAdminInertiaRequests;
use App\Models\Bom;
use App\Models\BomVersion;
use App\Models\Location;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(HandleAdminInertiaRequests::class)
    ->namespace('App\Http\Controllers\Admin')
    ->group(function () {

        Route::middleware('guest')->group(function () {
            Route::get('login', 'AuthController@show')
                ->name('auth.show');
            Route::post('login', 'AuthController@login')
                ->name('auth.login');
        });

        Route::middleware('auth')->group(function () {
            Route::post('logout', 'AuthController@logout')
                ->name('auth.logout');

            Route::get('', 'HomeController@show')
                ->name('home');

            /* Application Settings */
            Route::prefix('application')
                ->name('application.')
                ->controller('ApplicationController')
                ->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::get('edit', 'edit')->name('edit');
                    Route::patch('', 'update')->name('update');
                });

            /* Company */
            Route::prefix('company')
                ->name('company.')
                ->middleware('permission:company.update')
                ->controller('CompanyController')
                ->group(function () {
                    Route::get('edit', 'edit')->name('edit');
                    Route::patch('', 'update')->name('update');
                });

            /* Addresses */
            Route::prefix('addresses')->name('addresses.')->controller('AddressesController')->group(function () {
                Route::post('user/{user}/create', 'userCreate')->name('user.create')->can('createAddress', 'user');
                Route::patch('user/{user}/{address}', 'userUpdate')
                    ->name('user.update')
                    ->scopeBindings()
                    ->can('editUserAddress', 'address');
                Route::patch('user/{user}/make-default/{address}', 'userMakeDefault')
                    ->name('user.make-default')
                    ->scopeBindings()
                    ->can('editUserAddress', 'address');
                Route::delete('user/{user}/delete/{address}', 'userDestroy')
                    ->name('user.destroy')
                    ->scopeBindings()
                    ->can('deleteUserAddress', 'address');
            });

            /* Users */
            Route::prefix('users')->name('users.')->controller('UsersController')->group(function () {
                Route::get('', 'index')->name('index')->can('viewAny', User::class);
                Route::delete('{user}', 'destroy')->name('destroy')->can('delete', 'user');
                Route::patch('{user}/restore', 'restore')->name('restore')->withTrashed()->can('restore', 'user');
                Route::get('create', 'create')->name('create')->can('create', User::class);
                Route::get('{user}/edit', 'edit')->name('edit')->withTrashed()->can('edit', 'user');

                /* Users Update */
                Route::prefix('update')->name('update.')->controller('UsersUpdateController')->group(function () {
                    Route::patch('employee-id/{user}', 'updateEmployeeId')
                        ->name('employee-id')
                        ->withTrashed()
                        ->can('updateEmployeeId', 'user');
                    Route::patch('username/{user}', 'updateUsername')
                        ->name('username')
                        ->withTrashed()
                        ->can('updateUsername', 'user');
                    Route::patch('company-profile/{user}', 'updateCompanyProfile')
                        ->name('company-profile')
                        ->withTrashed()
                        ->can('updateCompanyProfile', 'user');
                    Route::patch('personal-profile/{user}', 'updatePersonalProfile')
                        ->name('personal-profile')
                        ->withTrashed()
                        ->can('updatePersonalProfile', 'user');
                    Route::patch('password/{user}', 'updatePassword')->name('password')->withTrashed();
                    Route::patch('protected-info/{user}', 'updateProtectedInfo')
                        ->name('protected-info')
                        ->withTrashed()
                        ->can('updateProtectedInfo', User::class);
                });

                Route::post('check-username', 'checkUsername')
                    ->name('check-username')
                    ->can('updateUsername', User::class);
                Route::post('check-employee-id', 'checkEmployeeId')
                    ->name('check-employee-id')
                    ->can('updateEmployeeId', User::class);
                Route::post('', 'store')->name('store')->can('create', User::class);
                Route::post('get-users', 'getUsers')->name('get-users')->can('viewAny', User::class);
            });

            /* Sales Orders */
            Route::prefix('sales-orders')
                ->name('sales-orders.')
                ->controller('SalesOrdersController')
                ->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::get('create', 'create')->name('create');
                    Route::post('stock-boms-search', 'stockBomSearch')->name('stock-boms-search');
                    Route::post('', 'store')->name('store');
                });

            /* Boms */
            Route::prefix('boms')->name('boms.')->controller('BomController')->group(function () {
                Route::get('', 'index')->name('index')->can('view', Bom::class);
                Route::get('{bom}/edit', 'edit')->name('edit')->can('edit', 'bom');
                Route::patch('{bom}', 'update')->name('update')->can('edit', 'bom');
                Route::get('create', 'create')->name('create')->can('boms.create');
                Route::post('store', 'store')->name('store')->can('boms.create');
                Route::post('name-exists', 'nameExists')->name('name-exists')->can('view', Bom::class);
                Route::post('get-boms', 'getBoms')->name('get-boms')->can('view', Bom::class);
                Route::post('get-versions', 'getVersions')->name('get-versions')->can('view', Bom::class);
            });

            Route::prefix('bom-versions')
                ->name('bom-versions.')
                ->controller('Boms\VersionsController')
                ->group(function () {
                    Route::get('index/{bom}', 'index')->name('index')->can('viewAny', BomVersion::class);
                    Route::get('bom-version/{bomVersion}', 'edit')->name('edit');
                    Route::patch('bom-version/{bomVersion}', 'update')->name('update');
                });

            /* Shifts */
            Route::prefix('shifts')
                ->name('shifts.')
                ->controller('ShiftController')
                ->group(function () {
                    Route::get('', 'index')
                        ->name('index')
                        ->can('view', Shift::class);

                    Route::get('create', 'create')
                        ->name('create')
                        ->can('create', Shift::class);

                    Route::post('get-items', 'getItems')
                        ->name('get-items')
                        ->can('view', Shift::class);

                    Route::get('{shift}/edit', 'edit')
                        ->name('edit')
                        ->can('edit', 'shift');

                    Route::patch('{shift}', 'update')
                        ->name('update')
                        ->can('edit', 'shift');

                    Route::post('name-exists', 'nameExists')
                        ->name('name-exists')
                        ->can('view', Shift::class);

                });

            /* Calendar */
            Route::prefix('calendars')
                ->name('calendars.')
                ->controller('CalendarController')
                ->group(function () {
                    Route::get('{calendar}/edit', 'edit')
                        ->name('edit')
                        ->can('edit', 'calendar');
                });

            /* Locations */
            Route::prefix('locations')
                ->name('locations.')
                ->controller('LocationsController')
                ->group(function () {
                    Route::get('', 'index')
                        ->name('index')
                        ->can('viewAny', Location::class);

                    Route::get('create', 'create')
                        ->name('create')
                        ->can('create', Location::class);

                    Route::patch('{location}', 'update')
                        ->name('update')
                        ->can('edit', 'location');

                    Route::post('', 'getLocations')
                        ->name('get-locations')
                        ->can('viewAny', Location::class);

                    Route::get('{location}/edit', 'edit')->name('edit')->can('edit', 'location');
                    Route::delete('{location}', 'destroy')->name('destroy')->can('delete', 'location');
                    Route::patch('{location}/restore', 'restore')
                        ->name('restore')
                        ->withTrashed()
                        ->can('restore', 'location');
                });
        });
    });
