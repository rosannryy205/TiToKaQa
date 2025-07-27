<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DialogflowService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(DialogflowService::class, function ($app) {
            return new DialogflowService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
