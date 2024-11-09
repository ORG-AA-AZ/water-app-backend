<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class UserResetPasswordRequest extends FormRequest
{
    public User $user;

    public function authorize(): bool
    {
        $this->user = User::where('mobile', $this->input('mobile'))->first();

        return $this->user()->can('update', $this->user);
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'mobile' => 'mobile number',
                'password' => 'password',
            ];
        } else {
            return [
                'mobile' => 'رقم الهاتف المحمول',
                'password' => 'كلمة المرور',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('messages.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('messages.regex', ['attribute' => $this->attributes()['mobile']]),
            'mobile.unique' => __('messages.unique', ['attribute' => $this->attributes()['mobile']]),
            'password.required' => __('messages.required', ['attribute' => $this->attributes()['password']]),
            'password.confirmed' => __('The password confirmation does not match.'),
        ];
    }
}
