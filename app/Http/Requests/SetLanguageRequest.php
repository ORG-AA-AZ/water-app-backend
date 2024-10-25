<?php

namespace App\Http\Requests;

use App\Enums\LanguagesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetLanguageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'lang' => [
                'required',
                Rule::in(LanguagesEnum::cases())
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'lang' => 'language',
        ];
    }

    public function messages(): array
    {
        return [
            'lang.required' => __('validation.required'),
            'lang.in' => __('validation.in'),
        ];
    }
}
