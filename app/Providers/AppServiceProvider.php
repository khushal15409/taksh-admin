<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\OrderCountService;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Models\ProductReturn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load helper functions for staging compatibility
        if (file_exists($helperFile = app_path('helpers.php'))) {
            require_once $helperFile;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Order Observer to clear cache on order changes
        Order::observe(OrderObserver::class);
        
        // Register ProductReturn Observer to clear cache on return changes
        ProductReturn::observe(function ($return) {
            OrderCountService::clearCache();
        });

        // Share order counts globally to all views (only for admin routes)
        View::composer('layouts.admin.*', function ($view) {
            // Only load counts for authenticated admin users
            if (auth()->check() && request()->is('admin*')) {
                $view->with('orderCounts', OrderCountService::getCounts());
            }
        });
    }
}
