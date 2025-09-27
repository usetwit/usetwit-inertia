<?php

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Intl\Countries;
use Tests\TestCase;

class AddressesFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserAndAddress(array $attributes = []): array
    {
        $user = User::factory()->create();
        $address = Address::factory()
            ->for($user, 'addressable')
            ->create(array_merge([
                'address_line_1' => '123 Example St',
                'address_line_2' => 'Suite 100',
                'address_line_3' => '',
                'postcode' => 'AB12 3CD',
                'country_code' => 'US',
                'is_default' => false,
            ], $attributes));

        return [$user, $address];
    }

    protected function postFromUserEdit(User $user, string $uri, array $data = []): TestResponse
    {
        return $this->from(route('admin.users.edit', $user))->post($uri, $data);
    }

    protected function patchFromUserEdit(User $user, string $uri, array $data = []): TestResponse
    {
        return $this->from(route('admin.users.edit', $user))->patch($uri, $data);
    }

    protected function deleteFromUserEdit(User $user, string $uri, array $data = []): TestResponse
    {
        return $this->from(route('admin.users.edit', $user))->delete($uri, $data);
    }

    public function test_create_address_with_permission(): void
    {
        $this->setUserWithPermissions('addresses.user.create');
        $targetUser = User::factory()->create();

        $payload = [
            'address_line_1' => '221B Baker St',
            'address_line_2' => 'Marylebone',
            'address_line_3' => 'London',
            'postcode' => 'NW1 6XE',
            'country_code' => 'GB',
        ];

        $response = $this->postFromUserEdit(
            $targetUser,
            route('admin.addresses.user.create', ['user' => $targetUser]),
            $payload
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $targetUser));

        $this->assertDatabaseHas('addresses', [
            'addressable_type' => User::class,
            'addressable_id' => $targetUser->id,
            'address_line_1' => $payload['address_line_1'],
            'postcode' => $payload['postcode'],
            'country_code' => $payload['country_code'],
            'country_name' => Countries::getName($payload['country_code'], config('app.locale')),
            'is_default' => true,
        ]);
    }

    public function test_create_address_without_permission_forbidden(): void
    {
        $this->setUserWithPermissions();

        $targetUser = User::factory()->create();
        $payload = [
            'address_line_1' => 'X',
            'postcode' => 'Y',
            'country_code' => 'US',
        ];

        $response = $this->post(
            route('admin.addresses.user.create', ['user' => $targetUser]),
            $payload
        );

        $response->assertStatus(403);
    }

    public function test_first_address_is_forced_default(): void
    {
        $this->setUserWithPermissions('addresses.user.create');
        $user = User::factory()->create();

        $payload = [
            'address_line_1' => '1 Test Rd',
            'postcode' => 'ZZ1 1ZZ',
            'country_code' => 'GB',
        ];

        $response = $this->postFromUserEdit(
            $user,
            route('admin.addresses.user.create', ['user' => $user]),
            $payload
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertDatabaseHas('addresses', [
            'addressable_type' => User::class,
            'addressable_id' => $user->id,
            'address_line_1' => $payload['address_line_1'],
            'is_default' => true,
        ]);
    }

    public function test_subsequent_address_respects_request_flag_and_unsets_previous(): void
    {
        $this->setUserWithPermissions('addresses.user.create');
        $user = User::factory()->create();

        $first = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => true]);

        $payload = [
            'address_line_1' => '2 Test Ave',
            'postcode' => 'YY2 2YY',
            'country_code' => 'US',
            'is_default' => true,
        ];

        $response = $this->postFromUserEdit(
            $user,
            route('admin.addresses.user.create', ['user' => $user]),
            $payload
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertDatabaseHas('addresses', [
            'id' => $first->id,
            'is_default' => false,
        ]);

        $new = Address::where('address_line_1', '2 Test Ave')->first();
        $this->assertTrue($new->is_default);
    }

    public function test_update_address_with_permission(): void
    {
        [$user, $address] = $this->createUserAndAddress();
        $this->setUserWithPermissions('addresses.user.update');

        $newData = [
            'address_line_1' => '10 Downing St',
            'postcode' => 'SW1A 2AA',
        ];

        $response = $this->patchFromUserEdit(
            $user,
            route('admin.addresses.user.update', compact('user', 'address')),
            $newData
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertDatabaseHas('addresses', array_merge(['id' => $address->id], $newData));
    }

    public function test_update_address_self_permission_allows_own_address(): void
    {
        [$user, $address] = $this->createUserAndAddress();

        Permission::firstOrCreate(['name' => 'addresses.user.update.self']);
        $user->givePermissionTo('addresses.user.update.self');

        $this->actingAs($user);

        $newPostcode = 'EC1A 1BB';

        $response = $this->patchFromUserEdit(
            $user,
            route('admin.addresses.user.update', compact('user', 'address')),
            [
                'address_line_1' => '10 Downing St',
                'postcode' => $newPostcode,
            ]
        );

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'postcode' => $newPostcode,
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));
    }

    public function test_make_default_unsets_previous_default(): void
    {
        $this->setUserWithPermissions('addresses.user.update');
        $user = User::factory()->create();

        $first = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => true]);
        $second = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => false]);

        $response = $this->patchFromUserEdit(
            $user,
            route('admin.addresses.user.make-default', ['user' => $user, 'address' => $second])
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertDatabaseHas('addresses', ['id' => $first->id, 'is_default' => false]);
        $this->assertDatabaseHas('addresses', ['id' => $second->id, 'is_default' => true]);
    }

    public function test_delete_address_with_permission(): void
    {
        [$user, $address] = $this->createUserAndAddress(['is_default' => true]);
        $this->setUserWithPermissions('addresses.user.delete');

        $response = $this->deleteFromUserEdit(
            $user,
            route('admin.addresses.user.destroy', compact('user', 'address'))
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'active' => 0]);
    }

    public function test_delete_address_self_permission_allows_own(): void
    {
        [$user, $address] = $this->createUserAndAddress();

        Permission::firstOrCreate(['name' => 'addresses.user.delete.self']);
        $user->givePermissionTo('addresses.user.delete.self');

        $this->actingAs($user);

        $response = $this->deleteFromUserEdit(
            $user,
            route('admin.addresses.user.destroy', compact('user', 'address'))
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
    }

    public function test_delete_address_forbidden_without_permission(): void
    {
        $this->setUserWithPermissions();

        [$user, $address] = $this->createUserAndAddress();

        $response = $this->delete(
            route('admin.addresses.user.destroy', compact('user', 'address'))
        );

        $response->assertStatus(403);
    }

    public function test_delete_default_assigns_new_latest_updated_default(): void
    {
        $this->setUserWithPermissions('addresses.user.delete');
        $user = User::factory()->create();

        $first = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => true]);

        $second = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => false]);

        $second->update(['address_line_1' => 'Updated']);

        $response = $this->deleteFromUserEdit(
            $user,
            route('admin.addresses.user.destroy', ['user' => $user, 'address' => $first])
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertSoftDeleted('addresses', ['id' => $first->id]);
        $this->assertDatabaseHas('addresses', ['id' => $first->id, 'active' => 0]);
        $this->assertDatabaseHas('addresses', ['id' => $second->id, 'is_default' => true]);
    }

    public function test_delete_non_default_does_not_change_existing_default(): void
    {
        $this->setUserWithPermissions('addresses.user.delete');
        $user = User::factory()->create();

        $first = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => true]);
        $second = Address::factory()
            ->for($user, 'addressable')
            ->create(['is_default' => false]);

        $response = $this->deleteFromUserEdit(
            $user,
            route('admin.addresses.user.destroy', ['user' => $user, 'address' => $second])
        );

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertDatabaseHas('addresses', ['id' => $first->id, 'is_default' => true]);
    }
}
