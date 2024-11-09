<?php

namespace App\Http\Controllers\Marketplace;

use App\Models\Marketplace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class MarketplaceDescriptionRequest extends FormRequest
{
    public Marketplace $marketplace;

    public function authorize(): bool
    {
        $this->marketplace = Marketplace::where('national_id', $this->input('national_id'))->first();

        return $this->user()->can('update', $this->marketplace);
    }

    public function rules(): array
    {
        return [
            'national_id' => ['required', 'string', 'regex:/^\d{10}$/'],
            'description' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'national_id' => 'national ID',
                'description' => 'description',
            ];
        } else {
            return [
                'national_id' => 'الرقم الوطني للمنشأة',
                'description' => 'الوصف',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'national_id.required' => __('messages.required', ['attribute' => $this->attributes()['national_id']]),
            'national_id.regex' => __('messages.regex', ['attribute' => $this->attributes()['national_id']]),
            'description.required' => __('messages.required', ['attribute' => $this->attributes()['description']]),
        ];
    }
}
