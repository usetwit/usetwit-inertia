<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'users.view.self']);
        Permission::create(['name' => 'users.update.self.personal-profile']);
        Permission::create(['name' => 'users.update.self.company-profile']);
        Permission::create(['name' => 'users.update.self.image']);
        Permission::create(['name' => 'users.update.self.password']);
        Permission::create(['name' => 'users.update.personal-profile']);
        Permission::create(['name' => 'users.update.company-profile']);
        Permission::create(['name' => 'users.update.image']);
        Permission::create(['name' => 'users.update.protected-info']);
        Permission::create(['name' => 'users.update.password']);
        Permission::create(['name' => 'addresses.user.create']);
        Permission::create(['name' => 'addresses.user.create.self']);
        Permission::create(['name' => 'addresses.user.update']);
        Permission::create(['name' => 'addresses.user.update.self']);
        Permission::create(['name' => 'addresses.user.delete']);
        Permission::create(['name' => 'addresses.user.delete.self']);
        Permission::create(['name' => 'addresses.customer.create']);
        Permission::create(['name' => 'addresses.customer.create.self']);
        Permission::create(['name' => 'addresses.customer.update']);
        Permission::create(['name' => 'addresses.customer.update.self']);
        Permission::create(['name' => 'addresses.customer.delete']);
        Permission::create(['name' => 'addresses.customer.delete.self']);
        Permission::create(['name' => 'addresses.company.create']);
        Permission::create(['name' => 'addresses.company.update']);
        Permission::create(['name' => 'addresses.company.delete']);
        Permission::create(['name' => 'company.update']);
        Permission::create(['name' => 'boms.update.self']);

        $methods = ['create', 'update', 'view', 'delete', 'restore'];
        $modules = ['users', 'roles', 'locations', 'shifts', 'calendars', 'sales-orders', 'invoices', 'boms', 'bom-versions'];

        foreach ($modules as $module) {
            foreach ($methods as $method) {
                Permission::create(['name' => $module.'.'.$method]);
            }
        }

        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'design']);
        Role::create(['name' => 'sales']);
        Role::create(['name' => 'purchasing']);
        Role::create(['name' => 'finance']);
        Role::create(['name' => 'operator']);
        Role::create(['name' => 'supervisor']);
        Role::create(['name' => 'manager']);
    }
}
