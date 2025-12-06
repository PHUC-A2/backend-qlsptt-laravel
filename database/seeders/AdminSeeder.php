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
        // 1. Lấy role ADMIN
        $adminRole = Role::where('name','ADMIN')->first();

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

        echo "Seeder: Tạo mới tài khoản admin thành công.\n";
    }
}
