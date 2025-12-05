<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Controllers\Controller;
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
    public function index()
    {
        $users = User::all();
        return $this->ok("Lấy tất cả người dùng", $users);
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

        return $this->success("Tạo người dùng thành công", $user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) return $this->error("Người dùng không tồn tại", 404);
        return $this->ok("Lấy người dùng thành công", $user);
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
        $user->roles()->sync($request->role_ids); // gắn role

        return response()->json([
            'status' => 200,
            'message' => 'Gán role cho users thành công',
            'data' => $user->roles
        ]);
    }
}
