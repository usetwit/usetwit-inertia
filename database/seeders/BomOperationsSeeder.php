<?php

namespace Database\Seeders;

use App\Models\BomOperation;
use Illuminate\Database\Seeder;

class BomOperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['x' => 140, 'y' => 50],
            ['x' => 140, 'y' => 230],
            ['x' => 270, 'y' => 140],
            ['x' => 420, 'y' => 20],
            ['x' => 420, 'y' => 140],
            ['x' => 760, 'y' => 140],
            ['x' => 530, 'y' => 20],
            ['x' => 850, 'y' => 140],
            ['x' => 940, 'y' => 140],
        ];

        for ($i = 1; $i <= 9; $i++) {
            BomOperation::factory()->create([
                'bom_version_id' => 1,
                'operation_id' => $i,
                'calendar_id' => 2,
                'type' => 'process',
                'buffer_duration_type' => null,
                'buffer_duration' => null,
                'x' => $positions[$i - 1]['x'],
                'y' => $positions[$i - 1]['y'],
            ]);
        }

        BomOperation::factory()->create([
            'bom_version_id' => 1,
            'operation_id' => null,
            'calendar_id' => 2,
            'type' => 'buffer',
            'buffer_duration_type' => 'calendar_day',
            'buffer_duration' => 2,
            'x' => 670,
            'y' => 140,
        ]);

        BomOperation::find(1)->successors()->sync([3]);
        BomOperation::find(2)->successors()->sync([3]);
        BomOperation::find(3)->successors()->sync([4, 5]);
        BomOperation::find(4)->successors()->sync([7]);
        BomOperation::find(5)->successors()->sync([10]);
        BomOperation::find(6)->successors()->sync([8]);
        BomOperation::find(7)->successors()->sync([10]);
        BomOperation::find(8)->successors()->sync([9]);
        BomOperation::find(10)->successors()->sync([6]);
    }
}
