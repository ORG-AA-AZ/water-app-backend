<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class MarketplaceRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'national_id' => ['required', 'string', 'regex:/^\d{10}$/', 'unique:marketplaces,national_id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'name' => 'name',
                'mobile' => 'mobile number',
                'national_id' => 'national ID',
                'password' => 'password',
                'latitude' => 'latitude',
                'longitude' => 'longitude',
            ];
        } else {
            return [
                'name' => 'الاسم',
                'mobile' => 'رقم الهاتف المحمول',
                'national_id' => 'الرقم الوطني للمنشأة',
                'password' => 'كلمة المرور',
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
            'name.required' => __('messages.required', ['attribute' => $this->attributes()['name']]),
            'national_id.required' => __('messages.required', ['attribute' => $this->attributes()['national_id']]),
            'national_id.unique' => __('messages.unique', ['attribute' => $this->attributes()['national_id']]),
            'national_id.regex' => __('messages.unique', ['attribute' => $this->attributes()['national_id']]),
            'password.required' => __('messages.required', ['attribute' => $this->attributes()['password']]),
            'password.confirmed' => __('The password confirmation does not match.'),
            'latitude.required' => __('messages.required', ['attribute' => $this->attributes()['latitude']]),
            'longitude.required' => __('messages.required', ['attribute' => $this->attributes()['longitude']]),
        ];
    }
}
