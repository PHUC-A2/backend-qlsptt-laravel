<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //  validate
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            // 'password' => 'required|confirmed', // password_confirmation
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                "status" => false,
                "meassage" => $errorMessage,
            ];

            return response()->json($response, 401);
        }


        // Create User

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        // gắn role VIEW tự động cho user mỗi khi đăng ký tài khoản
        $viewRoleId = Role::where('name', 'VIEW')->value('id');

        if ($viewRoleId) {
            $user->roles()->sync([$viewRoleId]);
        }

        // Response
        return response()->json([
            "status" => true,
            "message" => "Đăng ký tài khoản thành công"
        ]);
    }

    public function login(Request $request)
    {
        //  validate
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                "status" => false,
                "meassage" => $errorMessage,
            ];

            return response()->json($response, 401);
        }

        // Check user by email
        $user = User::where("email", $request->email)->first();

        //  Check user by password
        if (!empty($user)) {

            // nếu mật khẩu hợp lệ
            if (Hash::check($request->password, $user->password)) {

                // Login is ok
                $tokenInfo = $user->createToken("api-token");
                $token = $tokenInfo->plainTextToken; // Token value

                return response()->json([
                    "status" => true,
                    "message" => "Đăng nhập thành công",
                    "access_token" => $token
                ]);
            } else {

                //  nếu mật khẩu không khớp
                return response()->json([
                    "status" => false,
                    "message" => "Sai mật khẩu đăng nhập"
                ]);
            }
        } else {
            // Thông tin đăng nhập không hợp lệ
            return response()->json([
                "status" => false,
                "message" => "Thông tin đăng nhập không hợp lệ"
            ]);
        }
    }

    // public function profile(Request $request)
    // {
    //     $userData = $request->user();

    //     return response()->json([
    //         "status" => true,
    //         "message" => "Profile infomation",
    //         "data" => $userData
    //     ]);
    // }
    // public function profile(Request $request)
    // {
    //     $user = $request->user()->load('roles.permissions'); // lấy kèm roles + permissions

    //     $formatted = [
    //         'id' => $user->id,
    //         'name' => $user->name,
    //         'email' => $user->email,
    //         'roles' => $user->roles->pluck('name')->values(),
    //         'permissions' => $user->getAllPermissions(),
    //         'roles_detail' => $user->roles->map(function ($role) {
    //             return [
    //                 'id' => $role->id,
    //                 'name' => $role->name,
    //                 'permissions' => $role->permissions->pluck('name')->values(),
    //             ];
    //         }),

    //     ];

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Profile information',
    //         'data' => $formatted
    //     ]);
    // }
    public function profile(Request $request)
    {
        $user = $request->user()->load('roles.permissions');

        // Tên các role full quyền
        $FULL_ACCESS_ROLES = ['ADMIN', 'SUPER_ADMIN'];

        $formatted = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,

            // roles dạng ["ADMIN", "STAFF"]
            'roles' => $user->roles->pluck('name')->values(),

            // permissions trực tiếp từ user
            'permissions' => $user->getAllPermissions(),

            // roles + permissions trong role
            'roles_detail' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->values(),
                ];
            })->values(),

            //Thêm field is_full_access
            'is_full_access' => $user->roles->pluck('name')
                ->intersect($FULL_ACCESS_ROLES)
                ->isNotEmpty(),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Profile information',
            'data' => $formatted
        ]);
    }



    // GET
    public function logout()
    {
        // to get all tokens off logged in user and delete that
        // để lấy tất cả mã thông báo khỏi người dùng đã đăng nhập và xóa mã thông báo đó

        request()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "Đăng xuất thành công",
        ]);
    }

    // GET
    public function  refreshToken()
    {

        // Login is ok
        $tokenInfo = request()->user()->createToken("api-token");
        $newToken = $tokenInfo->plainTextToken; // Token value

        return response()->json([
            "status" => true,
            "message" => "Refresh token",
            "access_token" => $newToken
        ]);
    }
}
