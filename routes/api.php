<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
   Những api nằm ngoài nhóm "Protected Routes" sẽ không cần token
*/

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

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
        Route::get('/users', [UserController::class, 'index'])->middleware('permission:GET_USER');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:POST_USER');
        Route::get('/users/{id}', [UserController::class, 'show'])->middleware('permission:GET_USER_DETAIL');
        Route::put('/users/{id}', [UserController::class, 'update'])->middleware('permission:PUT_USER');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('permission:DELETE_USER');

        // api gắn/cập nhật roles cho users
        Route::put('/users/{id}/assign-roles', [UserController::class, 'assignRoles'])->middleware('permission:PUT_ASSIGN_ROLE');

        // api gắn permissions cho roles
        Route::post('/roles/{id}/assign-permissions', [RoleController::class, 'assignPermissions'])->middleware('permission:POST_ASSIGN_PERMISSION');

        // api produts
        // Route::get('/products', [ProductController::class, 'index'])->middleware('permission:GET_PRODUCT');
        Route::post('/products', [ProductController::class, 'store'])->middleware('permission:POST_PRODUCT');
        // Route::get('/products/{id}', [ProductController::class, 'show'])->middleware('permission:GET_PRODUCT_DETAIL');
        Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('permission:PUT_PRODUCT');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('permission:DELETE_PRODUCT');

        // api roles
        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:GET_ROLE');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:POST_ROLE');
        Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:GET_ROLE_DETAIL');
        Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:PUT_ROLE');;
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:DELETE_ROLE');


        // api permissions
        Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:GET_PERMISSION');
        Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:POST_PERMISSION');
        Route::get('/permissions/{id}', [PermissionController::class, 'show'])->middleware('permission:GET_PERMISSION_DETAIL');
        Route::put('/permissions/{id}', [PermissionController::class, 'update'])->middleware('permission:PUT_PERMISSION');;
        Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->middleware('permission:DELETE_PERMISSION');


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
