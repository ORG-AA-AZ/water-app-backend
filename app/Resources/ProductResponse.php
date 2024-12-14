<?php

namespace App\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'description' => $this->description,
            'image' => $this->image,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'marketplace_id' => $this->marketplace_id,
        ];
    }
}
