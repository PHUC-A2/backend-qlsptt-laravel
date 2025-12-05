<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\AssignPermissionRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * Lấy danh sách tất cả Roles
     */
    public function index()
    {
        $roles = Role::all();
        return $this->ok("Lấy tất cả roles", $roles);
    }

    /**
     * Tạo mới một Role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255'
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        return $this->success("Tạo role thành công", $role, 201);
    }

    /**
     * Hiển thị chi tiết Role
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error("Role không tồn tại", 404);
        }

        return $this->ok("Lấy role thành công", $role);
    }

    /**
     * Cập nhật Role
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error("Role không tồn tại", 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return $this->ok("Cập nhật role thành công", $role);
    }

    /**
     * Xóa Role
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error("Role không tồn tại", 404);
        }

        $role->delete();

        return $this->ok("Xóa role thành công");
    }

    /**
     * gắn permission cho roles
     */

    public function assignPermissions(AssignPermissionRequest $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $role->permissions()->sync($request->permission_ids);

        return response()->json([
            'status' => 200,
            'message' => 'Permissions được gắn thành công cho roles',
            'role' => $role->load('permissions')
        ]);
    }
}
