<?php

namespace App\Http\Controllers\Marketplace;

use App\Models\Marketplace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class MarketplaceLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'national_id' => ['required', 'string', 'regex:/^\d{10}$/'],
            'password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'national_id' => 'national ID',
                'password' => 'password',
            ];
        } else {
            return [
                'national_id' => 'الرقم الوطني للمنشأة',
                'password' => 'كلمة المرور',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'national_id.required' => __('messages.required', ['attribute' => $this->attributes()['national_id']]),
            'national_id.regex' => __('messages.regex', ['attribute' => $this->attributes()['national_id']]),
            'national_id.unique' => __('messages.unique', ['attribute' => $this->attributes()['national_id']]),
            'password.required' => __('messages.required', ['attribute' => $this->attributes()['password']]),
            'password.confirmed' => __('The password confirmation does not match.'),
        ];
    }
}
