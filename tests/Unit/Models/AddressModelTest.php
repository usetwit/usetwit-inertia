<?php

namespace Models;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Intl\Countries;
use Tests\TestCase;

class AddressModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sets_country_name_on_create(): void
    {
        $address = Address::factory()->create([
            'country_code' => 'US',
        ]);

        $this->assertEquals(
            Countries::getName('US', config('app.locale')),
            $address->country_name
        );
    }

    public function test_it_sets_country_name_on_make_and_save(): void
    {
        $address = Address::factory()->make([
            'country_code' => 'FR',
        ]);

        $address->save();

        $this->assertEquals(
            Countries::getName('FR', config('app.locale')),
            $address->country_name
        );
    }

    public function test_it_unsets_previous_default_when_new_default_is_saved(): void
    {
        $user = User::factory()->create();

        $first = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => true]);

        $second = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => true]);

        $first->refresh();

        $this->assertFalse($first->is_default, 'Old default should be unset');
        $this->assertTrue($second->is_default, 'New address should be default');
    }

    public function test_default_scope_returns_only_default_addresses(): void
    {
        Address::factory()->create(['is_default' => false]);
        $default = Address::factory()->create(['is_default' => true]);

        $results = Address::default()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($default));
    }

    public function test_it_sets_active_to_zero_when_soft_deleted(): void
    {
        $address = Address::factory()->create(['active' => 1]);

        $address->delete();

        $trashed = Address::withTrashed()->find($address->id);

        $this->assertEquals(0, $trashed->active);
    }

    public function test_it_sets_active_to_one_when_restored(): void
    {
        $address = Address::factory()->create(['active' => 1]);

        $address->delete();
        $address->restore();

        $restored = Address::find($address->id);

        $this->assertEquals(1, $restored->active);
    }
}
