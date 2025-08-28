<?php

namespace Database\Seeders;

use App\Models\Bom;
use App\Models\BomVersion;
use Illuminate\Database\Seeder;

class BomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BomVersion::factory()->for(Bom::factory())->create();

        Bom::factory(25)->create([
            'user_id' => 1,
        ]);
    }
}
