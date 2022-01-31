<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Gate::define('dashboard-customer', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-budgets', function ($user) {
            return $user->isAdmin();
        });
        
        Gate::define('manage-categories', function ($user) {
            return $user->isAdmin();
        });
        
        Gate::define('manage-contacts', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-customers', function ($user) {
            return $user->isAdmin();
        });
        
        Gate::define('manage-employees', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-orders', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-products', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-purchases', function ($user) {
            return $user->isAdmin();
        });
        
        Gate::define('manage-receivables', function ($user) {
            return $user->isAdmin();
        });
        
        Gate::define('manage-sales', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-services', function ($user) {
            return $user->isAdmin();
        });
                
        Gate::define('manage-service-orders', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-subscribers', function ($user) {
            return $user->isSuperAdmin();
        });

        Gate::define('manage-suppliers', function ($user) {
            return $user->isAdmin();
        });
        
        Gate::define('manage-targets', function ($user) {
            return $user->isSuperAdmin();
        });
        
        Gate::define('manage-units', function ($user) {
            return $user->isSuperAdmin();
        });
        
        Gate::define('setting-app', function ($user) {
            return $user->isSuperAdmin();
        });
        
        Gate::define('setting-subscriber', function ($user) {
            return $user->isAdmin();
        });
    }
}
