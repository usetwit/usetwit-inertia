<?php

namespace App\Providers;

use App\Services\BomComparisonService;
use App\Services\BomUpversionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BomComparisonService::class, function () {
            return new BomComparisonService;
        });

        $this->app->singleton(BomUpversionService::class, function () {
            return new BomUpversionService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();

        Route::pattern('calendar', '[0-9]+');
        Route::pattern('year', '^\d{4}$');
    }
}
