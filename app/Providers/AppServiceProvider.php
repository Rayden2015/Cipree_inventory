<?php

namespace App\Providers;

use App\Console\Commands\ProductionAwareSeedCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Sentry\Laravel\ServiceProvider as SentryServiceProvider;
use App\View\Composers\TenantBrandingComposer;

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
        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);
        
        // Share tenant branding with all views
        View::composer('*', TenantBrandingComposer::class);

        // Replace framework db:seed with a clearer production warning (ConfirmableTrait still applies).
        $this->app->booted(function () {
            if (! $this->app->runningInConsole()) {
                return;
            }

            $this->app->singleton(SeedCommand::class, function ($app) {
                return new ProductionAwareSeedCommand($app['db']);
            });
        });
    }
}