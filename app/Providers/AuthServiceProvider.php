<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Define dashboard access gates based on roles
        Gate::define('super-authoriser-dashboard', function ($user) {
            return $user->hasRole('Super Authoriser');
        });

        Gate::define('department-authoriser-dashboard', function ($user) {
            return $user->hasRole('Department Authoriser');
        });
    }
}