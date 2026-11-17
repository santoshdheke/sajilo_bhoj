<?php

namespace App\Providers;

use App\Console\Commands\FreshCommand;
use Illuminate\Database\Console\Migrations\FreshCommand as BaseFreshCommand;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // migrate:fresh also wipes the module databases (restaurant, shop, school), not only the default one.
        $this->app->extend(BaseFreshCommand::class, fn ($command, $app) => new FreshCommand($app['migrator']));
    }

    /**
     * Bootstrap any application services.
     *
     * Restaurant-specific wiring (routes, middleware, view composers, schedules)
     * lives in Modules\Restaurant\Providers\RestaurantServiceProvider.
     */
    public function boot(): void
    {
        // The app is styled with Bootstrap, not Tailwind: use Bootstrap page links.
        Paginator::useBootstrapFive();
    }
}
