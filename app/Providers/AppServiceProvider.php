<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\InventoryService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(InventoryService::class, function () {
            return new InventoryService();
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
