<?php

namespace App\Policies;

use App\Models\Marketplace;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(Marketplace $auth_marketplace, Product $product): bool
    {
        return $auth_marketplace->id === $product->marketplace_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Marketplace $auth_marketplace, Product $product): bool
    {
        return $auth_marketplace->id === $product->marketplace_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Marketplace $auth_marketplace, Product $product): bool
    {
        return $auth_marketplace->id === $product->marketplace_id;
    }
}
