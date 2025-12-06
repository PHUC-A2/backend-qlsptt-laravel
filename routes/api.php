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

        // api gắn/cập nhật roles cho users
        Route::put('/users/{id}/assign-roles', [UserController::class, 'assignRoles']);

        // api gắn permissions cho roles
        Route::post('/roles/{id}/assign-permissions', [RoleController::class, 'assignPermissions']);

        // api produts
        Route::get('/products', [ProductController::class, 'index'])
            ->middleware('permission:GET_PRODUCT');
        Route::post('/products', [ProductController::class, 'store'])
            ->middleware('permission:CREATE_PRODUCT');;
        Route::get('/products/{id}', [ProductController::class, 'show'])
            ->middleware('permission:GET_PRODUCT_DETAIL');;
        Route::put('/products/{id}', [ProductController::class, 'update'])
            ->middleware('permission:UPDATE_PRODUCT');;
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])
            ->middleware('permission:DELETE_PRODUCT');;

        // api roles
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{id}', [RoleController::class, 'show']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

        // api permissions
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::post('/permissions', [PermissionController::class, 'store']);
        Route::get('/permissions/{id}', [PermissionController::class, 'show']);
        Route::put('/permissions/{id}', [PermissionController::class, 'update']);
        Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);

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
