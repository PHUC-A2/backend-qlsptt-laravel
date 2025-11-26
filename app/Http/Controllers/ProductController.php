<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Product::all();
        return response()->json([
            "data" => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
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

        $product = Product::create([
            'name' => $request->name,
            'image_url' =>  $request->image_url,
            'description' =>  $request->description,
            'type' =>  $request->type,
            'price' =>  $request->price,
            'quantity' =>  $request->quantity,
        ]);

        return response()->json([
            "message" => "Tạo sản phẩm thành công",
            "data" => $product
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(["message" => "Sản phẩm không tồn tại"], 404);
        return response()->json(["data" => $product], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(["message" => "Sản phẩm không tồn tại"], 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',

        ]);

        $product->update([
            'name' => $request->name,
            'image_url' =>  $request->image_url,
            'description' =>  $request->description,
            'type' =>  $request->type,
            'price' =>  $request->price,
            'quantity' =>  $request->quantity,
        ]);

        return response()->json([
            "message" => "Cập nhật sản phẩm thành công",
            "data" => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $peoduct = Product::find($id);
        if (!$peoduct) return response()->json(["message" => "Sản phẩm không tồn tại"], 404);
        $peoduct->delete();
        return response()->json(["message" => "Sản phẩm đã bị xóa"], 200);
    }
}
