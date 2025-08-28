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
        $bom = Bom::factory()->create();

        foreach (range(1, 9) as $i) {
            BomVersion::factory()->for($bom)->create([
                'version' => $i,
            ]);
        }

        Bom::factory(25)->create([
            'user_id' => 1,
        ]);
    }
}
