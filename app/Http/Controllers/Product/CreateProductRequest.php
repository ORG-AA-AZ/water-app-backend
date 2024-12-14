<?php

namespace App\Http\Controllers\Product;

use App\Models\Marketplace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class CreateProductRequest extends FormRequest
{
    public ?Marketplace $marketplace;

    public function authorize(): bool
    {
        $this->marketplace = Marketplace::where('id', $this->input('marketplace_id'))?->first();        
        return $this->user()->can('update', $this->marketplace);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'brand' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
            'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'quantity' => ['required', 'integer'],
            'marketplace_id' => ['required'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'name' => 'name',
                'brand' => 'brand',
                'description' => 'description',
                'image' => 'image',
                'price' => 'price',
                'quantity' => 'quantity',
            ];
        } else {
            return [
                'name' => 'الاسم',
                'brand' => 'الماركة',
                'description' => 'الوصف',
                'image' => 'الصورة',
                'price' => 'السعر',
                'quantity' => 'الكمية',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.required', ['attribute' => $this->attributes()['name']]),
            'brand.required' => __('messages.required', ['attribute' => $this->attributes()['brand']]),
            'description.required' => __('messages.required', ['attribute' => $this->attributes()['description']]),
            'image.required' => __('messages.required', ['attribute' => $this->attributes()['image']]),
            'price.required' => __('messages.required', ['attribute' => $this->attributes()['price']]),
            'price.regex' => __('messages.decimal_max', ['attribute' => $this->attributes()['price'], 'decimal' => 2]),
            'quantity.required' => __('messages.required', ['attribute' => $this->attributes()['quantity']]),
            'quantity.integer' => __('messages.required', ['attribute' => $this->attributes()['quantity']]),
        ];
    }
}
