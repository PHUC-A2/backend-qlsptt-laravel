<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use ApiResponse;

    /**
     * Danh sách tất cả permission
     */
    public function index()
    {
        $permissions = Permission::all();
        return $this->ok("Lấy tất cả permissions", $permissions);
    }

    /**
     * Tạo permission mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:255'
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return $this->success("Tạo permission thành công", $permission, 201);
    }

    /**
     * Lấy chi tiết permission
     */
    public function show($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->error("Permission không tồn tại", 404);
        }

        return $this->ok("Lấy permission thành công", $permission);
    }

    /**
     * Cập nhật permission
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->error("Permission không tồn tại", 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'description' => 'nullable|string|max:255'
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return $this->ok("Cập nhật permission thành công", $permission);
    }

    /**
     * Xóa permission
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->error("Permission không tồn tại", 404);
        }

        $permission->delete();

        return $this->ok("Xóa permission thành công");
    }
}
