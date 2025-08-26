<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            CalendarSeeder::class,
            BomSeeder::class,
            StockItemSeeder::class,
            LocationsSeeder::class,
            BomSeeder::class,
            OperationsSeeder::class,
            BomOperationsSeeder::class,
        ]);
    }
}
