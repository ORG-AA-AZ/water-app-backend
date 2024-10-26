<?php

namespace App\Http\Controllers\Language;

use App\Enums\LanguagesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class SetLanguageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'lang' => [
                'required',
                Rule::in(LanguagesEnum::cases()),
            ],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'lang' => 'language',
            ];
        } else {
            return [
                'lang' => 'اللغة',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'lang.required' => __('messages.required'),
            'lang.in' => __('messages.in'),
        ];
    }
}
