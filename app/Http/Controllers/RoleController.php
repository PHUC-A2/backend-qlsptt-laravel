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
    // public function index()
    // {
    //     $roles = Role::all();
    //     return $this->ok("Lấy tất cả roles", $roles);
    // }
    public function index()
    {
        // Lấy tất cả role + permissions + pivot
        $roles = Role::with('permissions')->get();

        // Format dữ liệu
        $formatted = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,

                // danh sách tên permissions
                'permissions' => $role->permissions->pluck('name')->values(),

                // trả full detail như API show
                'permissions_detail' => $role->permissions->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'description' => $p->description,
                        'created_at' => $p->created_at,
                        'updated_at' => $p->updated_at,
                        'pivot' => $p->pivot // role_id + permission_id
                    ];
                }),

                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ];
        });

        return $this->ok("Lấy tất cả roles", $formatted);
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
    // public function show($id)
    // {
    //     $role = Role::find($id);

    //     if (!$role) {
    //         return $this->error("Role không tồn tại", 404);
    //     }

    //     return $this->ok("Lấy role thành công", $role);
    // }
    public function show($id)
    {
        // Lấy role + permission theo bảng role_permissions
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return $this->error("Role không tồn tại", 404);
        }

        // Format dữ liệu trả về
        $formatted = [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->values(), // chỉ trả về tên
            'permissions_detail' => $role->permissions, // trả full object
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];

        return $this->ok("Lấy role thành công", $formatted);
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
