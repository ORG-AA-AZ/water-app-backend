<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetLocationRequest extends FormRequest
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
        return [
            'mobile' => 'mobile number',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('validation.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('validation.regex', ['attribute' => $this->attributes()['mobile']]),
            'latitude.required' => __('validation.required', ['attribute' => $this->attributes()['latitude']]),
            'longitude.required' => __('validation.required', ['attribute' => $this->attributes()['longitude']]),
        ];
    }
}
