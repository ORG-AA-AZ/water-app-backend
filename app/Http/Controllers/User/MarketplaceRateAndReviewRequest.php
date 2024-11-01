<?php

namespace App\Http\Controllers\User;

use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class MarketplaceRateAndReviewRequest extends FormRequest
{
    public User $user;
    public Marketplace $marketplace;

    public function authorize(): bool
    {
        if(is_null(Marketplace::where('national_id', $this->input('national_id'))->first()))
        {
            return false;
        }
    
        if($this->user() instanceof User)
        {
            $this->marketplace = Marketplace::where('national_id', $this->input('national_id'))->first();
            $this->user = User::where('mobile', $this->input('mobile'))->first();
            return true;
        } else {
            return false;
        }
    }

    public function rules(): array
    {
        return [
            'national_id' => ['required', 'string', 'regex:/^\d{10}$/'],
            'mobile' => ['required', 'string', 'regex:/^\d{10}$/'],
            'rate' => ['required', 'numeric', 'between:1,5'],
            'review' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        if (App::getLocale() === 'en') {
            return [
                'national_id' => 'national ID',
                'mobile' => 'mobile number',
                'rate' => 'rate',
                'review' => 'review',
            ];
        } else {
            return [
                'national_id' => 'الرقم الوطني للمنشأة',
                'mobile' => 'رقم الهاتف',
                'rate' => 'التقييم',
                'review' => 'مراجعة',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'national_id.required' => __('messages.required', ['attribute' => $this->attributes()['national_id']]),
            'national_id.regex' => __('messages.regex', ['attribute' => $this->attributes()['national_id']]),
            'mobile.required' => __('messages.required', ['attribute' => $this->attributes()['mobile']]),
            'mobile.regex' => __('messages.regex', ['attribute' => $this->attributes()['mobile']]),
            'rate.required' => __('messages.required', ['attribute' => $this->attributes()['rate']]),
            'review.required' => __('messages.required', ['attribute' => $this->attributes()['review']]),
        ];
    }
}
