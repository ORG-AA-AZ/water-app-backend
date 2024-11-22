<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;

class ProductController
{
    public function createProduct(CreateProductRequest $request)
    {
        try {
            Product::create([
                'name' => $request->input('name'),
                'brand' => $request->input('brand'),
                'description' => $request->input('description'),
                'image' => $request->input('image'),
                'price' => $request->input('price'),
                'quantity' => $request->input('quantity'),
                'marketplace_id' => $request->marketplace->id,
            ]);

            return response()->json(['message' => __('messages.add_product_successfully')]);

        } catch(\Exception $e) {
            return response()->json([
                'error' => __('messages.invaild_add_product'),
            ]);
        }
    }
}
