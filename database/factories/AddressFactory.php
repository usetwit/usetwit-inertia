<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Symfony\Component\Intl\Countries;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $country_code = $this->faker->countryCode;
        $country_name = Countries::getName($country_code, app()->getLocale());

        return [
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->city,
            'address_line_3' => $this->faker->country,
            'postcode' => $this->faker->postcode,
            'country_code' => $country_code,
            'country_name' => $country_name,
            'addressable_type' => $this->faker->randomElement([Customer::class, User::class, Location::class, Company::class]),
            'addressable_id' => function (array $attributes) {
                return match ($attributes['addressable_type']) {
                    Customer::class => CustomerFactory::new()->create()->id,
                    Location::class => LocationFactory::new()->create()->id,
                    User::class => UserFactory::new()->create()->id,
                    Company::class => CompanyFactory::new()->create()->id,
                };
            },
            'is_default' => false,
            'deleted_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
