<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Ví dụ dùng: ->middleware('permission:CREATE_PRODUCT')
     * Có thể truyền nhiều permission: permission:CREATE_PRODUCT|PUT_PRODUCT
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user(); // Lấy user đã đăng nhập

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Nếu user có role ADMIN -> bỏ qua hết kiểm tra permission
        if ($user->roles()->where('name', 'ADMIN')->exists()) {
            return $next($request);
        }

        // Cho phép truyền nhiều permission, ngăn cách bằng |
        $requiredPermissions = explode('|', $permission);

        // Lấy tất cả permission user có (từ các role)
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        // Nếu có ít nhất 1 permission phù hợp -> được phép
        $allowed = collect($requiredPermissions)
            ->intersect($userPermissions)
            ->isNotEmpty();

        if (!$allowed) {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden: bạn không có quyền ' . $permission,
            ], 403);
        }

        return $next($request);
    }
}
