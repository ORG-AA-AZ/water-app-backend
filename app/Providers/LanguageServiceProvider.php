<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $locale = request('lang', Session::get('locale', 'en'));
        Session::put('locale', $locale);
        App::setLocale($locale);
    }

    public function register()
    {
        // No bindings needed here
    }
}
