<?php

namespace Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_with_full_name_using_create(): void
    {
        User::factory()->create([
            'first_name' => 'John',
            'middle_names' => 'Michael',
            'last_name' => 'Doe',
        ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'middle_names' => 'Michael',
            'last_name' => 'Doe',
            'full_name' => 'John Michael Doe',
        ]);
    }

    public function test_it_creates_a_user_with_full_name_using_make(): void
    {
        $user = User::factory()->make([
            'first_name' => 'John',
            'middle_names' => 'Michael',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Michael Doe', $user->full_name);
    }

    public function test_it_updates_full_name_after_saving(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'middle_names' => 'Michael',
            'last_name' => 'Doe',
        ]);

        $user->first_name = 'Jane';
        $user->middle_names = 'Ann';
        $user->last_name = 'Smith';
        $user->save();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'full_name' => 'Jane Ann Smith',
        ]);
    }

    public function test_it_updates_full_name_after_saving_with_make(): void
    {
        $user = User::factory()->make([
            'first_name' => 'John',
            'middle_names' => 'Michael',
            'last_name' => 'Doe',
        ]);

        $user->first_name = 'Jane';
        $user->middle_names = 'Ann';
        $user->last_name = 'Smith';
        $user->save();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'full_name' => 'Jane Ann Smith',
        ]);
    }

    public function test_it_sets_active_to_zero_when_user_is_soft_deleted()
    {
        $user = User::factory()->create(['active' => 1]);

        $user->delete();

        $user = User::withTrashed()->find($user->id);

        $this->assertEquals(0, $user->active);
    }

    public function test_it_sets_active_to_one_when_user_is_restored()
    {
        $user = User::factory()->create(['active' => 1]);

        $user->delete();
        $user->restore();

        $user = User::find($user->id);

        $this->assertEquals(1, $user->active);
    }

    public function test_it_does_not_modify_active_column_when_user_is_not_deleted_or_restored()
    {
        $user = User::factory()->create();

        $this->assertEquals(1, $user->active);
    }
}
