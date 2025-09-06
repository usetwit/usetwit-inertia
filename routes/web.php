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
            Route::prefix('addresses')
                ->name('addresses.')
                ->controller('AddressController')
                ->group(function () {
                    Route::post('user/{user}/create', 'userCreate')
                        ->name('user.create')
                        ->can('createAddress', 'user');

                    Route::patch('update/{address}', 'userUpdate')
                        ->name('user.update')
                        ->can('updateUserAddress', 'address');

                    Route::patch('user-make-default/{address}', 'makeDefault')
                        ->name('user.make-default')
                        ->can('updateUserAddress', 'address');

                    Route::delete('delete/{address}', 'userDestroy')
                        ->name('user.destroy')
                        ->scopeBindings()
                        ->can('deleteUserAddress', 'address');
                });

            /* Users */
            Route::prefix('users')
                ->name('users.')
                ->controller('UserController')
                ->group(function () {
                    Route::get('', 'index')
                        ->name('index')
                        ->can('view', User::class);

                    Route::get('create', 'create')
                        ->name('create')
                        ->can('create', User::class);

                    Route::post('get-users', 'getUsers')
                        ->name('get-users')
                        ->can('view', User::class);

                    Route::get('{user}/edit', 'edit')
                        ->name('edit')
                        ->can('update', 'user');

                    Route::post('username-exists', 'usernameExists')
                        ->name('username-exists')
                        ->can('view', User::class);

                    Route::post('employee-id-exists', 'employeeIdExists')
                        ->name('employee-id-exists')
                        ->can('view', User::class);

                    Route::prefix('update')->name('update.')->controller('UserUpdateController')->group(function () {

                        Route::patch('personal-profile/{user}', 'updatePersonalProfile')
                            ->name('personal-profile')
                            ->withTrashed()
                            ->can('updatePersonalProfile', 'user');

                        Route::patch('company-profile/{user}', 'updateCompanyProfile')
                            ->name('company-profile')
                            ->withTrashed()
                            ->can('updateCompanyProfile', 'user');
                    });

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

                Route::get('{bom}/edit', 'edit')
                    ->name('edit')
                    ->can('update', 'bom');

                Route::patch('{bom}', 'update')
                    ->name('update')
                    ->can('update', 'bom');

                Route::get('create', 'create')
                    ->name('create')
                    ->can('boms.create');

                Route::post('store', 'store')
                    ->name('store')
                    ->can('boms.create');

                Route::post('name-exists', 'nameExists')
                    ->name('name-exists')
                    ->can('view', Bom::class);

                Route::post('get-boms', 'getBoms')
                    ->name('get-boms')
                    ->can('view', Bom::class);

                Route::post('{bom}/get-versions', 'getVersions')
                    ->name('get-versions')
                    ->can('view', Bom::class);
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
                        ->can('update', 'shift');

                    Route::patch('{shift}', 'update')
                        ->name('update')
                        ->can('update', 'shift');

                    Route::post('name-exists', 'nameExists')
                        ->name('name-exists')
                        ->can('view', Shift::class);

                });

            /* CalendarShifts Shifts */
            Route::prefix('calendar-shifts')
                ->name('calendar-shifts.')
                ->controller('CalendarShiftsController')
                ->group(function () {
                    Route::get('{calendar}/edit', 'edit')
                        ->name('edit')
                        ->can('update', 'calendar');

                    Route::put('{calendar}', 'update')
                        ->name('update')
                        ->can('update', 'calendar');

                    Route::post('{calendar}/get-shifts', 'getShifts')
                        ->name('get-shifts')
                        ->can('update', 'calendar');
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
                        ->can('update', 'location');

                    Route::post('', 'getLocations')
                        ->name('get-locations')
                        ->can('viewAny', Location::class);

                    Route::get('{location}/edit', 'edit')->name('edit')->can('update', 'location');
                    Route::delete('{location}', 'destroy')->name('destroy')->can('delete', 'location');
                    Route::patch('{location}/restore', 'restore')
                        ->name('restore')
                        ->withTrashed()
                        ->can('restore', 'location');
                });
        });
    });
