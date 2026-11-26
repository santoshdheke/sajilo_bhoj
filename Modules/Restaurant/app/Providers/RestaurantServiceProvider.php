<?php

namespace Modules\Restaurant\Providers;

use App\Models\Company as BaseCompany;
use App\Support\Workspace;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Modules\Restaurant\Console\Commands\ReferralWithdrawals;
use Modules\Restaurant\Console\Commands\SettleBilling;
use Modules\Restaurant\Http\Controllers\Admin\SettingController;
use Modules\Restaurant\Http\Middleware\EnsureUserHasPermission;
use Modules\Restaurant\Http\Middleware\ResolveTenant;
use Modules\Restaurant\Models\NavMenuItem;
use Modules\Restaurant\Models\Setting;
use Modules\Restaurant\Models\User;
use Modules\Restaurant\Support\Money;
use Modules\Restaurant\Support\SocialLinks;
use Modules\Restaurant\Support\Tenancy;
use Nwidart\Modules\Support\ModuleServiceProvider;

class RestaurantServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Restaurant';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'restaurant';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        SettleBilling::class,
        ReferralWithdrawals::class,
    ];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->registerWorkspace();
        $this->registerMiddleware();
        $this->registerViewComposers();
    }

    /**
     * How the shared workspace logic (App\Support\Workspace) treats restaurants: a restaurant
     * sign-up is finished once the owner verified their email; until then only the verify-code
     * screen and logout work. The sign-up page works on hosts without a company.
     */
    private function registerWorkspace(): void
    {
        BaseCompany::verifyUsing(BaseCompany::TYPE_RESTAURANT, fn (BaseCompany $company) => User::where('company_id', $company->id)
            ->whereNotNull('email_verified_at')->exists());

        Workspace::allowWithoutWorkspace(['restaurant.register.*']);
        Workspace::allowWhileUnverified(['admin.verify_email.*', 'admin.logout']);
    }

    /**
     * Prepaid subscriptions: take the hourly plan charges and pause the ones that ran out of balance.
     * (Also happens on admin/website visits; this keeps idle restaurants up to date.)
     */
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('billing:settle')->hourly()->withoutOverlapping();
    }

    private function registerMiddleware(): void
    {
        $kernel = $this->app->make(HttpKernel::class);

        // The tenant must be known before route models are loaded ({menuCategory}, {order}, ...),
        // otherwise the branch/company scopes are not applied and any id from any restaurant resolves.
        $kernel->addToMiddlewarePriorityBefore(SubstituteBindings::class, ResolveTenant::class);

        $this->app->make(Router::class)->aliasMiddleware('permission', EnsureUserHasPermission::class);

        // Guest redirects are global: only set them on restaurant hosts (the shop sets its own).
        if (! Workspace::serves(BaseCompany::TYPE_RESTAURANT) && ! Workspace::isUnclaimedHost()) {
            return;
        }

        $redirectGuests = fn (Request $request) => $request->is('admin/*')
            ? route('admin.auth.show_login_form')
            : route('frontend.auth.show_login_form');

        Authenticate::redirectUsing($redirectGuests);
        AuthenticateSession::redirectUsing($redirectGuests);
        AuthenticationException::redirectUsing($redirectGuests);
    }

    private function registerViewComposers(): void
    {
        View::composer('restaurant::frontend.theme_one.*', function ($view) {
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
                 ->with('socialLinks', SocialLinks::forCompany($company?->id))
                 ->with('currencyCode', $settings['currency'] ?: 'NPR')
                 ->with('currencySymbol', Money::symbol($settings['currency'] ?: 'NPR'))
                 ->with($this->brand($company, $settings['restaurant_name'] ?? ''));
        });

        View::composer('restaurant::admin.theme_one.*', function ($view) {
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
