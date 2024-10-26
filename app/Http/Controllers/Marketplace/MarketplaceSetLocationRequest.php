<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class MarketplaceSetLocationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'mobile' => 'mobile number',
                'latitude' => 'latitude',
                'longitude' => 'longitude',
            ];
        } else {
            return [
                'mobile' => 'رقم الهاتف المحمول',
                'latitude' => 'خط العرض',
                'longitude' => 'خط الطول',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('messages.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('messages.regex', ['attribute' => $this->attributes()['mobile']]),
            'latitude.required' => __('messages.required', ['attribute' => $this->attributes()['latitude']]),
            'longitude.required' => __('messages.required', ['attribute' => $this->attributes()['longitude']]),
        ];
    }
}
