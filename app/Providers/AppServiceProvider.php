<?php

namespace App\Providers;

use App\Http\Controllers\Admin\SettingController;
use App\Models\NavMenuItem;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Storage;
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

            $view->with('settings', $settings)->with('navMenuItems', $navMenuItems)
                 ->with($this->brand($company, $settings['restaurant_name'] ?? ''));
        });

        View::composer('admin.theme_one.*', function ($view) {
            $themeColor = SettingController::SECTIONS['theme']['fields']['theme_color'];

            if ($company = Tenancy::company()) {
                $themeColor = Setting::getValue($company->id, 'theme_color', $themeColor);
            }

            $company = Tenancy::company();
            $name    = $company ? Setting::getValue($company->id, 'restaurant_name', '') : '';

            $view->with('themeColor', $themeColor)->with($this->brand($company, $name));
        });
    }

    /**
     * Brand data for the shared logo partial: the uploaded company logo (if any)
     * and the business name to show next to the default logo mark.
     */
    private function brand($company, ?string $restaurantName): array
    {
        $logo = $company?->logo;

        return [
            'brandName'    => $restaurantName ?: ($company?->name ?: 'SajiloBhoj'),
            'brandLogoUrl' => $logo && Storage::disk('public')->exists($logo) ? Storage::url($logo) : null,
        ];
    }
}
