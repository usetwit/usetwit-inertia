<?php

namespace App\Providers;

use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Support\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FilterService::class, function ($app) {
            return new FilterService($app->make(GeneralSettings::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
