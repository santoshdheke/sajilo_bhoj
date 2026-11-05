<?php

namespace App\Providers;

use App\Http\Controllers\Admin\SettingController;
use App\Models\NavMenuItem;
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
            $settings = SettingController::allDefaults();
            $company  = Tenancy::company();

            if ($company) {
                foreach ($settings as $key => $default) {
                    $settings[$key] = Setting::getValue($company->id, $key, $default);
                }
            }

            $navMenuItems = $company
                ? NavMenuItem::forCompany($company->id)
                : collect(NavMenuItem::defaults())->map(fn ($item) => (object) $item);

            $view->with('settings', $settings)->with('navMenuItems', $navMenuItems);
        });

        View::composer('admin.theme_one.*', function ($view) {
            $themeColor = SettingController::SECTIONS['theme']['fields']['theme_color'];

            if ($company = Tenancy::company()) {
                $themeColor = Setting::getValue($company->id, 'theme_color', $themeColor);
            }

            $view->with('themeColor', $themeColor);
        });
    }
}
