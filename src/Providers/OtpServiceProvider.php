<?php

namespace Tzsk\Otp\Providers;

use Tzsk\Otp\OtpManager;
use Illuminate\Support\ServiceProvider;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/otp.php', 'otp');

        // Register the service the package provides.
        $this->app->singleton('tzsk-otp', function ($app) {
            $digits = config('otp.digits');
            $expiry = config('otp.expiry');

            return new OtpManager($digits, $expiry);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['tzsk-otp'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/otp.php' => config_path('otp.php'),
        ], 'tzsk-otp');
    }
}
