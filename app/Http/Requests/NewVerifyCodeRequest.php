<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewVerifyCodeRequest extends FormRequest
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
            'mobile.required' => __('validation.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('validation.regex', ['attribute' => $this->attributes()['mobile']]),
        ];
    }
}
