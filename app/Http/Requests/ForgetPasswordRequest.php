<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
        ];
    }
    
    public function attributes(): array
    {
        return [
            'mobile' => 'mobile number',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('validation.required'),
            'mobile.regex' => __('validation.regex'),
        ];
    }
}
