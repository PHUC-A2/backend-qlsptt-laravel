<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // sử dụng traits
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return $this->ok("Lấy tất cả sản phẩm", $products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::create($request->all());

        return $this->success("Tạo sản phẩm thành công", $product,201);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) return $this->error("Sản phẩm không tồn tại", 404);
        return $this->ok("Lấy sản phẩm thành công", $product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return $this->error("Sản phẩm không tồn tại", 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return $this->ok("Cập nhật sản phẩm thành công", $product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return $this->error("Sản phẩm không tồn tại", 404);
        $product->delete();
        return $this->ok("Sản phẩm đã bị xóa");
    }
}
