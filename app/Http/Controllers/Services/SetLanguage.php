<?php

namespace App\Http\Controllers\Services;

use App\Http\Requests\SetLanguageRequest;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    public function SetLanguage(SetLanguageRequest $request)
    {
        $locale = $request->input('lang') ?? 'ar';
        App::setLocale($locale);

        return response()->json(['message' =>  __('messages.set_language_successfully')]);
    }
}
