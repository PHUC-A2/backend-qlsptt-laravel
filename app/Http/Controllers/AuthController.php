<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "User registered successfully"
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
                    "message" => "Login successfully",
                    "access_token" => $token
                ]);
            } else {

                //  nếu mật khẩu không khớp
                return response()->json([
                    "status" => false,
                    "message" => "Password didn't match"
                ]);
            }
        } else {
            // Thông tin đăng nhập không hợp lệ
            return response()->json([
                "status" => false,
                "message" => "Invalid credentials"
            ]);
        }
    }

    public function profile(Request $request)
    {
        $userData = $request->user();

        return response()->json([
            "status" => true,
            "message" => "Profile infomation",
            "data" => $userData
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
            "message" => "User logged out",
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
