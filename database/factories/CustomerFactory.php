<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    protected static ?string $password;

    public function definition(): array
    {
        $type = Arr::random(['B2B', 'B2C']);

        return [
            'type' => $type,
            'first_name' => $type === 'B2C' ? $this->faker->name() : null,
            'last_name' => $type === 'B2C' ? $this->faker->name() : null,
            'company_name' => $type === 'B2B' ? $this->faker->company() : null,
            'password' => static::$password ??= Hash::make('password'),
            'slug' => fn(array $attributes) => Str::slug($type === 'B2C' ? "{$attributes['first_name']} {$attributes['last_name']}" : $attributes['company_name']),
            'comments' => $this->faker->optional()->text(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
