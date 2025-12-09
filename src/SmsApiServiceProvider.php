<?php

namespace Mpap\LaravelSmsApiMailer;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Mpap\LaravelSmsApiMailer\Transport\SmsApiTransport;

class SmsApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/sms-api.php',
            'sms-api'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/sms-api.php' => config_path('sms-api.php'),
            ], 'sms-api-config');
        }

        Mail::extend('smsapi', function (array $config) {
            return new SmsApiTransport(
                $config['api_url'] ?? config('sms-api.api_url'),
                $config['token'] ?? config('sms-api.token'),
                $config['sistema'] ?? config('sms-api.sistema')
            );
        });
    }
}
