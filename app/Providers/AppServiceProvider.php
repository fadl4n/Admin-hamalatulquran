<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once __DIR__ . '/../Helpers/CommonHelper.php';
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MENU COMPOSER
        view()->composer([
            'layout.sidebar'
        ], 'App\ViewComposers\SidebarMenuComposer');
    }
}
