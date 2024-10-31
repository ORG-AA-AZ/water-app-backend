<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class MarketplaceForgetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'national_id' => ['required', 'string', 'regex:/^\d{10}$/'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'national_id' => 'national ID',
            ];
        } else {
            return [
                'national_id' => 'الرقم الوطني للمنشأة',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'national_id.required' => __('messages.required'),
            'national_id.regex' => __('messages.regex'),
        ];
    }
}
