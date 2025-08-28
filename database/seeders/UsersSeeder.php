<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role['admin'] = Role::findByName('admin');

        $mike = User::create([
            'username' => 'mike',
            'password' => Hash::make('x'),
            'first_name' => 'Michael',
            'middle_names' => 'James',
            'last_name' => 'Maynard',
            'full_name' => 'Michael Maynard',
            'employee_id' => 'E00002',
            'email' => 'mike@usetwit.com',
            'active' => true,
            'joined_at' => '2024-01-01',
        ])->assignRole($role['admin']);

        Address::factory()
            ->count(3)
            ->for($mike, 'addressable')
            ->create();

        Address::factory()
            ->for($mike, 'addressable')
            ->state(['is_default' => true])
            ->create();

        $filePath = storage_path('app/images/user/profile/1/b0f8b49f22c718e9924f5b1165111a67.png');
        $img = null;

        if (file_exists($filePath)) {
            $img = InterventionImage::read($filePath);

            $img = [
                'filename' => 'b0f8b49f22c718e9924f5b1165111a67.png',
                'type' => 'user_profile',
                'hash' => hash_file('sha256', $filePath),
                'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
                'mime_type' => mime_content_type($filePath),
                'comments' => 'Lee\'s profile picture',
                'size' => filesize($filePath),
                'width' => $img->width(),
                'height' => $img->height(),
                'is_default' => true,
                'imageable_id' => $mike->id,
                'imageable_type' => User::class,
            ];
        } else {
            $this->command->error("File not found: $filePath");
        }

        if ($img) {
            $mike->uploadedImages()->create($img);
        }

        User::create([
            'username' => 'jade',
            'password' => Hash::make('password'),
            'first_name' => 'Jade',
            'last_name' => 'Harvey',
            'full_name' => 'Jade Harvey',
            'employee_id' => 'E00003',
            'email' => 'jade@rivauk.co.uk',
            'active' => true,
            'joined_at' => '2024-01-01',
        ])->assignRole($role['admin']);

        $roles = Role::where('name', '!=', 'admin')->get();

        User::factory(100)
            ->create()
            ->each(function (User $user) use ($roles) {
                $user->assignRole($roles->random());
            });
    }
}
