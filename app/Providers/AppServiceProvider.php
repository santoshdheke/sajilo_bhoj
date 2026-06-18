<?php

namespace App\Providers;

use App\Http\Controllers\Admin\SettingController;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('frontend.theme_one.*', function ($view) {
            $settings = SettingController::DEFAULTS;

            if ($company = Tenancy::company()) {
                foreach ($settings as $key => $default) {
                    $settings[$key] = Setting::getValue($company->id, $key, $default);
                }
            }

            $view->with('settings', $settings);
        });

        View::composer('admin.theme_one.*', function ($view) {
            $themeColor = SettingController::DEFAULTS['theme_color'];

            if ($company = Tenancy::company()) {
                $themeColor = Setting::getValue($company->id, 'theme_color', $themeColor);
            }

            $view->with('themeColor', $themeColor);
        });
    }
}
