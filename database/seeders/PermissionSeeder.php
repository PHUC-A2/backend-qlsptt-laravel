<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách permission
        $permissions = [
            ['name' => 'GET_PRODUCT', 'description' => 'Xem tất cả sản phẩm'],
            ['name' => 'GET_PRODUCT_DETAIL', 'description' => 'Xem chi tiết sản phẩm'],
            ['name' => 'CREATE_PRODUCT', 'description' => 'Tạo sản phẩm mới'],
            ['name' => 'PUT_PRODUCT', 'description' => 'Cập nhật sản phẩm'],
            ['name' => 'DELETE_PRODUCT', 'description' => 'Xóa sản phẩm'],
            // thêm các permission khác ở đây
        ];

        // Lặp qua mảng và tạo nếu chưa có
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }

        echo "Seeder: Tạo mới permission thành công.\n";
    }
}
