<?php

namespace Tests;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Generate a long string.
     */
    protected function longString(int $length = 256, bool $isNumeric = false): string
    {
        return str_repeat($isNumeric ? '1' : 'a', $length);
    }

    protected function setUserWithPermissions(array|string $permissions = []): User
    {
        $user = $this->createUserWithPermissions($permissions);

        $this->actingAs($user);

        return $user;
    }

    /**
     * Create a Closure for custom Rules
     */
    public function failClosure(string &$errorMessage): Closure
    {
        return function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        };
    }

    /**
     * Create a user with given permissions.
     */
    protected function createUserWithPermissions(array|string $permissions = []): User
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $user = User::factory()->create();

        $role = Role::findOrCreate('test-role');

        if ($permissions) {
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
            }

            $role->syncPermissions($permissions);
        }

        $user->assignRole($role);

        return $user;
    }

    protected function createTestTable(): void
    {
        Schema::create('test_table', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }
}
