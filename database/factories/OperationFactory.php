<?php

namespace Database\Factories;

use App\Models\Operation;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OperationFactory extends Factory
{
    protected $model = Operation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'color' => $this->faker->randomElement(['red', 'green', 'blue', 'yellow', 'teal']),
            'cost_ph' => $this->faker->randomFloat(2, 12.5, 30),
            'deleted_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
