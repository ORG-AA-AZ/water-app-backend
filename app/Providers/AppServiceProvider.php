<?php

namespace App\Providers;

use App\Services\Sms\FakeSmsService;
use App\Services\Sms\ServiceTwilioSms;
use App\Services\Sms\SmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! $this->app->environment('production')) {
            $this->app->singleton(ServiceTwilioSms::class, FakeSmsService::class);

            return;
        }

        $this->app->singleton(ServiceTwilioSms::class, SmsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
