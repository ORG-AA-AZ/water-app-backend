<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class UpdateProductRequest extends FormRequest
{
    public ?Product $product;

    public function authorize(): bool
    {
        $this->product = Product::find($this->input('id'));

        return $this->product 
            && $this->product->marketplace_id === $this->user()->id
            && $this->user()->can('update', $this->product);
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:products,id'],
            'name' => ['sometimes', 'string'],
            'brand' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'image' => ['sometimes', 'string'],
            'price' => ['sometimes', 'regex:/^\d+(\.\d{1,2})?$/'],
            'quantity' => ['sometimes', 'integer'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'price' => 'price',
                'quantity' => 'quantity',
            ];
        } else {
            return [
                'price' => 'السعر',
                'quantity' => 'الكمية',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'price.regex' => __('messages.decimal_max', ['attribute' => $this->attributes()['price'], 'decimal' => 2]),
            'quantity.integer' => __('messages.integer', ['attribute' => $this->attributes()['quantity']]),
        ];
    }
}
