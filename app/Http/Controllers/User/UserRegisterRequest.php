<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

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
        if (App::getLocale() === 'en') {
            return [
                'name' => 'name',
                'mobile' => 'mobile number',
                'password' => 'password',
            ];
        } else {
            return [
                'name' => 'الاسم',
                'mobile' => 'رقم الهاتف المحمول',
                'password' => 'كلمة المرور',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.required', ['attribute' => $this->attributes()['name']]),
            'mobile.required' => __('messages.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('messages.regex', ['attribute' => $this->attributes()['mobile']]),
            'mobile.unique' => __('messages.unique', ['attribute' => $this->attributes()['mobile']]),
            'password.required' => __('messages.required', ['attribute' => $this->attributes()['password']]),
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
