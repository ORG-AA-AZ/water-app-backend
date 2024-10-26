<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class VerifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'mobile' => 'mobile number',
                'code' => 'verify code',
            ];
        } else {
            return [
                'mobile' => 'رقم الهاتف المحمول',
                'code' => 'رمز التأكيد',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('messages.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('messages.regex', ['attribute' => $this->attributes()['mobile']]),
            'code.required' => __('messages.required', ['attribute' => $this->attributes()['code']]),
            'code.regex' => __('messages.regex', ['attribute' => $this->attributes()['code']]),
        ];
    }
}
