<?php

namespace Tzsk\Otp;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Tzsk\Otp\Commands\OtpPublishCommand;

class OtpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/otp.php' => config_path('otp.php'),
            ], 'otp-config');

            $this->commands([
                OtpPublishCommand::class,
            ]);
        }

        $this->app->bind('tzsk-otp', function () {
            ['digits' => $digits, 'expiry' => $expiry] = config('otp');

            return (new Otp(Cache::store()))->digits($digits)->expiry($expiry);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/otp.php', 'otp');
    }
}
