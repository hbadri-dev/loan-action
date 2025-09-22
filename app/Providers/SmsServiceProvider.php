<?php

namespace App\Providers;

use App\Services\SMS\KavenegarService;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(KavenegarService::class, function ($app) {
            return new KavenegarService();
        });

        $this->app->alias(KavenegarService::class, 'sms.kavenegar');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

