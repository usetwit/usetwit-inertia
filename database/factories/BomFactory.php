<?php

namespace Database\Factories;

use App\Models\Bom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BomFactory extends Factory
{
    protected $model = Bom::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => strtoupper($this->faker->word),
            'description' => $this->faker->sentence,
            'active' => true,
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
        ];
    }
}
