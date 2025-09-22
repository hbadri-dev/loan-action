<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'buyer']);
        Role::firstOrCreate(['name' => 'seller']);

        // Create admin user if doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'ادمین',
                'password' => bcrypt('admin123'),
                'phone' => '09123456789',
                'phone_verified_at' => now(),
                'is_phone_verified' => true,
            ]
        );

        $admin->assignRole('admin');
    }
}
