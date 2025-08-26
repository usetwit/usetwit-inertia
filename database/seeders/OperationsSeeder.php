<?php

namespace Database\Seeders;

use App\Models\Operation;
use Illuminate\Database\Seeder;

class OperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operations = [
            'CUTTING-1', 'CUTTING-2',
            'SEWING-1', 'SEWING-2', 'SEWING-3', 'SEWING-4',
            'QC-SEWING-1', 'QC-SEWING-2',
            'PALLETIZE',
        ];

        foreach ($operations as $operation) {
            Operation::factory()->create([
                'name' => $operation,
            ]);
        }
    }
}
