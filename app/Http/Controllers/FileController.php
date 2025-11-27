<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    // POST /api/v1/files/upload
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'folder' => 'sometimes|string'
        ]);

        $file = $request->file('file');
        $folder = $request->input('folder', 'products');

        // tạo tên file duy nhất
        $filename = time() . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        // lưu file vào storage/app/public/{folder}
        $path = $file->storeAs($folder, $filename, 'public');

        // tạo URL truy cập file
        $url = asset("storage/{$path}");

        // trả JSON như Spring
        return response()->json([
            'fileName' => $filename,
            'uploadedAt' => now(),
            'url' => $url
        ]);
    }
}
