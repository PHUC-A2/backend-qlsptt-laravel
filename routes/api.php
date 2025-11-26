<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
   Những api nằm ngoài nhóm "Protected Routes" sẽ không cần token
*/

// api auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected Routes
Route::group(
    [
        "middleware" => ["auth:sanctum"]
    ],
    function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('refresh', [AuthController::class, 'refreshToken']);
    }
);


Route::get("/", function () {
    return response()->json([
        "data" => "Hello"
    ], 200);
});
