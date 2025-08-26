<?php

namespace Database\Factories;

use App\Models\BomOperation;
use App\Models\BomVersion;
use App\Models\Calendar;
use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

class BomOperationFactory extends Factory
{
    protected $model = BomOperation::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['process', 'buffer']);

        if ($type === 'buffer') {
            $bufferDurationType = $this->faker->randomElement(['minutes', 'calendar_day', 'working_day']);

            if ($bufferDurationType === 'minutes') {
                $bufferDuration = $this->faker->numberBetween(120, 1200);
            } else {
                $bufferDuration = $this->faker->numberBetween(1, 3);
            }
        } else {
            $bufferDurationType = null;
            $bufferDuration = null;
        }

        return [
            'bom_version_id' => BomVersion::factory(),
            'operation_id' => Operation::factory(),
            'calendar_id' => Calendar::factory(),
            'type' => $type,
            'buffer_duration_type' => $bufferDurationType,
            'cost_ph' => $this->faker->randomFloat(2, 15, 50),
            'x' => $this->faker->numberBetween(0, 1000),
            'y' => $this->faker->numberBetween(0, 1000),
            'color' => $this->faker->randomElement(['red', 'green', 'blue', 'yellow', 'teal']),
            'buffer_duration' => $bufferDuration,
            'instructions' => $this->faker->optional()->sentence(),
        ];
    }
}
