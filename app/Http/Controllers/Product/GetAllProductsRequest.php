<?php

namespace App\Http\Controllers\Product;

use Illuminate\Foundation\Http\FormRequest;

class GetAllProductsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'marketplace_id' => ['required', 'string', 'exists:marketplaces,id']
        ];
    }
}
