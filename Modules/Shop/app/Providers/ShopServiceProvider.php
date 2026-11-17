<?php

namespace Modules\Shop\Providers;

use App\Models\Company as BaseCompany;
use App\Support\Workspace;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Modules\Shop\Models\Customer;
use Modules\Shop\Models\ShopSetting;
use Modules\Shop\Models\User;
use Modules\Shop\Support\ShopTenancy;
use Nwidart\Modules\Support\ModuleServiceProvider;

/**
 * Shop management system (ported from multiple_pasal): its own database ("shop" connection),
 * login guards for staff ("shop") and storefront customers ("shop_customer"), and routes only
 * on shop domains (see RouteServiceProvider).
 */
class ShopServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Shop';

    protected string $nameLower = 'shop';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        config([
            // Shop staff log in with their own guard, so a restaurant and a shop session never mix.
            'auth.guards.shop'          => ['driver' => 'session', 'provider' => 'shop_users'],
            'auth.providers.shop_users' => ['driver' => 'eloquent', 'model' => User::class],
            // Storefront shoppers (customers table), separate from the shop staff.
            'auth.guards.shop_customer'     => ['driver' => 'session', 'provider' => 'shop_customers'],
            'auth.providers.shop_customers' => ['driver' => 'eloquent', 'model' => Customer::class],
        ]);
    }

    public function boot(): void
    {
        parent::boot();

        $this->registerWorkspace();
        $this->registerGuestRedirect();
        $this->registerViewComposers();
    }

    /**
     * How the shared workspace logic treats shops: the sign-up page works on hosts without a
     * company; a shop is live once its owner's email is verified (done before sign-up, by code).
     */
    private function registerWorkspace(): void
    {
        BaseCompany::verifyUsing(BaseCompany::TYPE_SHOP, fn (BaseCompany $company) => User::withoutGlobalScopes()
            ->where('company_id', $company->id)->whereNotNull('email_verified_at')->exists());

        Workspace::allowWithoutWorkspace(['shop.register.*']);
    }

    /**
     * Guests on a shop domain go to the right login: back-office pages (/admin) to the staff login,
     * customer account pages to the storefront login. Guest redirects are global, so only on shop hosts.
     */
    private function registerGuestRedirect(): void
    {
        if ($this->app->runningInConsole() || Workspace::type() !== BaseCompany::TYPE_SHOP) {
            return;
        }

        $toLogin = fn (Request $request) => $request->is('admin', 'admin/*')
            ? route('shop.admin.auth.show_login_form')
            : route('shop.front.login');

        Authenticate::redirectUsing($toLogin);
        AuthenticateSession::redirectUsing($toLogin);
        AuthenticationException::redirectUsing($toLogin);
    }

    private function registerViewComposers(): void
    {
        // Back office: shop name / logo, and the shop's brand colour as the admin theme colour.
        View::composer('shop::admin.theme_one.*', function ($view) {
            $company = ShopTenancy::company();

            $view->with('shopName', $company?->name ?: 'SajiloBhoj Shop')
                 ->with('shopLogoUrl', self::logoUrl($company))
                 ->with('themeColor', $company ? '#'.ShopSetting::current()->brand()['primary'] : '#2563eb');
        });

        // Storefront theme "simple_theme": what multiple_pasal read from company() / logoColor().
        View::composer('shop::frontend.simple_theme.*', function ($view) {
            static $shared = null; // once per request (the theme has many partials)

            $shared ??= (function () {
                $company  = ShopTenancy::company();
                $settings = ShopSetting::current();

                return [
                    'shop'        => $company,
                    'settings'    => $settings,
                    'brand'       => $settings->brand(),
                    'socialLinks' => $settings->socialLinks(),
                    'shopLogoUrl' => self::logoUrl($company),
                    'theme_path'  => 'shop::frontend.simple_theme.',
                    'view_path'   => 'shop::frontend.simple_theme.home.',
                ];
            })();

            $view->with($shared);
        });
    }

    private static function logoUrl(?BaseCompany $company): ?string
    {
        $logo = $company?->logo;

        return $logo && Storage::disk('public')->exists($logo) ? Storage::disk('public')->url($logo) : null;
    }
}
