<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // dùng trait
    use ApiResponse;

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
            return $this->error($errorMessage, 401);
        }

        // Create User
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        return $this->success("Đăng ký tài khoản thành công", $user, 201);
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
            return $this->notAuthorized($errorMessage);
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
                return $this->success("Đăng nhập thành công", $token, 200);
            } else {

                //  nếu mật khẩu không khớp
                return $this->error("Sai mật khẩu", 401);
            }
        } else {

            // Thông tin đăng nhập không hợp lệ
            return $this->error("Thông tin đăng nhập không hợp lệ", 401);
        }
    }

    public function profile(Request $request)
    {
        $userData = $request->user();
        return $this->ok("Tài khoản cá nhân là", $userData, 200);
    }

    // POST
    public function logout()
    {
        // to get all tokens off logged in user and delete that
        // để lấy tất cả mã thông báo khỏi người dùng đã đăng nhập và xóa mã thông báo đó

        request()->user()->tokens()->delete();

        return $this->ok("Đăng xuất thành công", 200);
    }

    // GET
    public function  refreshToken()
    {

        // Login is ok
        $tokenInfo = request()->user()->createToken("api-token");
        $newToken = $tokenInfo->plainTextToken; // Token value

        return $this->ok("Refresh token", $newToken);
    }
}
