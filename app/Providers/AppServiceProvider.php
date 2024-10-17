<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
<<<<<<< HEAD
use Sentry\Laravel\ServiceProvider as SentryServiceProvider;
=======
use Illuminate\Support\ServiceProvider;
>>>>>>> d29d2b411f82256fddca149984e6cef765ac5ec9

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        if ($this->app->isProduction()) {
            $this->app->bind('sentry', function () {
                return app('sentry')->captureMessage('Test Sentry Setup');
            });
            $this->app->register(SentryServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
    }
}
