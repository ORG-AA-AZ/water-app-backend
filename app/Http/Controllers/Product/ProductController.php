<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Resources\ProductResponse;
use Illuminate\Support\Facades\Cache;

class ProductController
{
    public function getAllProduct(GetAllProductsRequest $request)
    {
        $marketplace_id = $request->input('marketplace_id');

        $products = Cache::remember(
            "products_marketplace_{$marketplace_id}",
            now()->addHours(6),
            function () use ($marketplace_id) {
                return Product::where('marketplace_id', $marketplace_id)->get();
            }
        );

        return ProductResponse::collection($products);
    }

    public function createProduct(CreateProductRequest $request)
    {
        Product::create([
            'name' => $request->input('name'),
            'brand' => $request->input('brand'),
            'description' => $request->input('description'),
            'image' => $request->input('image'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'marketplace_id' => $request->marketplace->id,
        ]);

        Cache::forget("products_marketplace_{$request->marketplace->id}");

        return response()->json(['message' => __('messages.add_product_successfully')]);
    }

    public function updateProduct(UpdateProductRequest $request)
    {
        $product = $request->product;

        $product->update($request->only([
            'name',
            'brand',
            'description',
            'image',
            'price',
            'quantity',
        ]));

        Cache::forget("products_marketplace_{$product->marketplace_id}");

        return response()->json(['message' => __('messages.update_product_successfully')]);
    }

    public function deleteProduct(string $id)
    {
        $product = Product::where('id', $id)->first();

        if (! $product) {
            return response()->json(['message' => __('messages.product_not_found')], 404);
        }

        $product->delete();

        Cache::forget("products_marketplace_{$product->marketplace_id}");

        return response()->json(['message' => __('messages.delete_product_successfully')]);
    }
}
