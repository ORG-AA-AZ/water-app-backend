<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'mobile' => 'mobile number',
            'code' => 'verify code',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('validation.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('validation.regex', ['attribute' => $this->attributes()['mobile']]),
            'code.required' => __('validation.required', ['attribute' => $this->attributes()['code']]),
            'code.regex' => __('validation.regex', ['attribute' => $this->attributes()['code']]),
        ];
    }
}
