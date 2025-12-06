<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách role ADMIN,VIEW...
        $roles = [
            ['name' => 'ADMIN'],
            ['name' => 'VIEW']
            // thêm các role khác ở đây
        ];

        // Lặp qua mảng và tạo nếu chưa có
        foreach ($roles as $perm) {
            Role::firstOrCreate(
                ['name' => $perm['name']]
            );
        }

        echo "Seeder: Tạo mới role thành công.\n";

        // Gắn permission cho role VIEW
        $viewRole = Role::where('name', 'VIEW')->first();
        if ($viewRole) {
            $viewPermissions = Permission::whereIn('name', [
                'GET_PRODUCT',
                'GET_PRODUCT_DETAIL'
            ])->get();

            $viewRole->permissions()->syncWithoutDetaching(
                $viewPermissions->pluck('id')->toArray()
            );

            echo "Seeder: Gắn permission cho role VIEW thành công.\n";
        }
    }
}
