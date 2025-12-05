<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
   Những api nằm ngoài nhóm "Protected Routes" sẽ không cần token
*/

// Route::get('/products', [ProductController::class, 'index']);

// upload
Route::post('/files/upload', [FileController::class, 'upload']);

// api auth
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Protected Routes
Route::group(
    [
        "middleware" => ["auth:sanctum"]
    ],
    function () {

        // api users
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // api produts
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // api auth
        Route::get('auth/profile', [AuthController::class, 'profile']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/refresh', [AuthController::class, 'refreshToken']);
    }
);


Route::get("/", function () {
    return response()->json([
        "data" => "Hello"
    ], 200);
});
// Laravel Passport

