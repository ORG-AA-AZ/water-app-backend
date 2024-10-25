<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'name',
            'mobile' => 'mobile number',
            'password' => 'password',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => $this->attributes()['name']]),
            'mobile.required' => __('validation.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('validation.regex', ['attribute' => $this->attributes()['mobile']]),
            'mobile.unique' => __('validation.unique', ['attribute' => $this->attributes()['mobile']]),
            'password.required' => __('validation.required', ['attribute' => $this->attributes()['password']]),
            'password.confirmed' => __('The password confirmation does not match.'),
        ];
    }
}
