<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('active', true);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    // Admin only
    public function store(Request $request)
    {
        $request->validate([
            'sku'         => 'required|string|max:20|unique:products,sku',
            'name'        => 'required|string|max:200',
            'category'    => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|string|max:500',
            'tag'         => 'nullable|string|max:50',
            'stock'       => 'nullable|integer|min:0',
        ]);

        $product = Product::create([
            'sku'         => strtoupper(strip_tags($request->sku)),
            'name'        => strip_tags($request->name),
            'category'    => strip_tags($request->category),
            'price'       => $request->price,
            'description' => $request->description ? strip_tags($request->description) : null,
            'image'       => $request->image,
            'tag'         => $request->tag ? strip_tags($request->tag) : null,
            'stock'       => $request->stock ?? 0,
            'active'      => true,
        ]);

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'sometimes|string|max:200',
            'category'    => 'sometimes|string|max:100',
            'price'       => 'sometimes|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|string|max:500',
            'tag'         => 'nullable|string|max:50',
            'stock'       => 'nullable|integer|min:0',
            'active'      => 'sometimes|boolean',
        ]);

        $product->update($request->only([
            'name', 'category', 'price', 'description',
            'image', 'tag', 'stock', 'active',
        ]));

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted.']);
    }
}
