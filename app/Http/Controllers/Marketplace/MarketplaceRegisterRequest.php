<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Foundation\Http\FormRequest;

class MarketplaceRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/', 'unique:marketplaces,mobile'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'national_id' => ['required', 'string', 'min:8', 'unique:marketplaces,national_id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function attributes(): array
    {
        return [
            'mobile' => 'mobile number',
            'national_id' => 'national ID',
            'password' => 'password',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('validation.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('validation.regex', ['attribute' => $this->attributes()['mobile']]),
            'mobile.unique' => __('validation.unique', ['attribute' => $this->attributes()['mobile']]),
            'national_id.required' => __('validation.required', ['attribute' => $this->attributes()['national_id']]),
            'national_id.unique' => __('validation.unique', ['attribute' => $this->attributes()['national_id']]),
            'password.required' => __('validation.required', ['attribute' => $this->attributes()['password']]),
            'password.confirmed' => __('The password confirmation does not match.'),
            'latitude.required' => __('validation.required', ['attribute' => $this->attributes()['latitude']]),
            'longitude.required' => __('validation.required', ['attribute' => $this->attributes()['longitude']]),
        ];
    }
}
