<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shift1 = Shift::factory()->create(['name' => '24 Hours']);
        $shift1->calendar()->create();

        $shift2 = Shift::factory()->create(['name' => 'RGB']);
        $shift2->calendar()->create();
    }
}
