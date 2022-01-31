<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') === 'production' || env('APP_ENV') === 'develop-ebs') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            Passport::loadKeysFrom('/etc/pki/tls/certs/');
        }
    }
}
