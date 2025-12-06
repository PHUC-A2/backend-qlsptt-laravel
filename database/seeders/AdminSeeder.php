<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo role ADMIN nếu chưa có
        $adminRole = Role::firstOrCreate(
            ['name' => 'ADMIN'],
        );

        // 2. Tạo user admin nếu chưa tồn tại
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('123456'),
            ]
        );

        // 3. Gắn role ADMIN cho user
        if (!$admin->roles()->where('name', 'ADMIN')->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        echo "Seeder: ADMIN user and role created/updated successfully.\n";
    }
}
