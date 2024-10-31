<?php

namespace App\Http\Controllers\Marketplace;

use App\Models\Marketplace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class MarketplaceSetLocationRequest extends FormRequest
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
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'national_id' => 'national ID',
                'latitude' => 'latitude',
                'longitude' => 'longitude',
            ];
        } else {
            return [
                'national_id' => 'الرقم الوطني للمنشأة',
                'latitude' => 'خط العرض',
                'longitude' => 'خط الطول',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'national_id.required' => __('messages.required', ['attribute' => $this->attributes()['national_id']]),
            'national_id.regex' => __('messages.regex', ['attribute' => $this->attributes()['national_id']]),
            'latitude.required' => __('messages.required', ['attribute' => $this->attributes()['latitude']]),
            'longitude.required' => __('messages.required', ['attribute' => $this->attributes()['longitude']]),
        ];
    }
}
