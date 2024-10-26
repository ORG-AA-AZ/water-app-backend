<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

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
        if(App::getLocale() === 'en')
        {
            return [
                'mobile' => 'mobile number',
            ];
        } else {
            return [
                'mobile' => 'رقم الهاتف المحمول',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('messages.required'),
            'mobile.regex' => __('messages.regex'),
        ];
    }
}
