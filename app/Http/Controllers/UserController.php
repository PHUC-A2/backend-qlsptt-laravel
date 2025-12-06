<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // sử dụng traits
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $users = User::all();
    //     return $this->ok("Lấy tất cả người dùng", $users);
    // }
    public function index()
    {
        // Load roles và permissions của role trong 1 query
        $users = User::with('roles.permissions')->get();

        // Chuẩn hoá dữ liệu trả ra
        $formatted = $users->map(function ($user) {
            return [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,

                // Danh sách role (theo tên)
                "roles" => $user->roles->pluck('name')->values(),

                // Danh sách permission (từ tất cả roles)
                "permissions" => $user->getAllPermissions(),

                // Nếu muốn xem raw roles + permissions dưới dạng object:
                "roles_detail" => $user->roles->map(function ($role) {
                    return [
                        "id" => $role->id,
                        "name" => $role->name,
                        "permissions" => $role->permissions->pluck('name'),
                    ];
                }),
            ];
        });

        return $this->ok("Lấy tất cả người dùng", $formatted);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // gắn role VIEW tự động cho user mỗi khi đăng ký tài khoản
        $viewRoleId = Role::where('name', 'VIEW')->value('id');

        if ($viewRoleId) {
            $user->roles()->sync([$viewRoleId]);
        }

        return $this->success("Tạo người dùng thành công", $user, 201);
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $user = User::find($id);
    //     if (!$user) return $this->error("Người dùng không tồn tại", 404);
    //     return $this->ok("Lấy người dùng thành công", $user);
    // }

    public function show(string $id)
    {
        // Lấy user kèm roles + permissions
        $user = User::with("roles.permissions")->find($id);

        if (!$user) {
            return $this->error("Người dùng không tồn tại", 404);
        }

        // Format dữ liệu trả ra
        $formatted = [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,

            // Tên các role
            "roles" => $user->roles->pluck('name')->values(),

            // Tất cả permissions của user (từ các role)
            "permissions" => $user->getAllPermissions(),

            // Chi tiết role + permission
            "roles_detail" => $user->roles->map(function ($role) {
                return [
                    "id" => $role->id,
                    "name" => $role->name,
                    "permissions" => $role->permissions->pluck('name')->values(),
                ];
            }),
        ];

        return $this->ok("Lấy người dùng thành công", $formatted);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) return $this->error("Người dùng không tồn tại", 404);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return $this->ok("Cập nhật người dùng thành công", $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) return $this->error("Người dùng không tồn tại", 404);
        $user->delete();
        return $this->ok("Người dùng đã bị xóa");
    }

    /*
        gắn vai trò cho user
    */

    public function assignRoles(Request $request, $id)
    {
        $request->validate([
            'role_ids' => 'required|array'
        ]);

        $user = User::findOrFail($id);

        $user->roles()->sync($request->role_ids);

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật roles cho user thành công',
            'user' => $user->load('roles')
        ]);
    }
}
