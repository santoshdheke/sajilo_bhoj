<?php

namespace Modules\Shop\Providers;

use App\Models\Company;
use App\Support\Workspace;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Middleware\ResolveShop;

/**
 * Shop routes are only registered for shop domains (company_type = shop), so a restaurant
 * domain can use the same URLs. The sign-up page is also there on hosts without a company.
 */
class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Shop';

    public function boot(): void
    {
        parent::boot();

        // The shop must be known before the logged-in user and route models load ({product},
        // {order}...), so the company scope applies and another shop's ids don't resolve.
        $this->app->make(HttpKernel::class)->prependToMiddlewarePriority(ResolveShop::class);
    }

    public function map(): void
    {
        if (Workspace::serves(Company::TYPE_SHOP) || Workspace::isUnclaimedHost()) {
            $this->mapRegisterRoutes();
        }

        if (Workspace::serves(Company::TYPE_SHOP)) {
            $this->mapWebRoutes();
            $this->mapAdminRoutes();
            $this->mapStorefrontRoutes(); // last: it ends with catch-all /{category}/{product}
        }
    }

    /**
     * Shop sign-up (/shop/register).
     */
    protected function mapRegisterRoutes(): void
    {
        Route::middleware('web')->group(module_path($this->name, '/routes/register.php'));
    }

    /**
     * Shop domain pages outside the back office: login, staff set-password.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', ResolveShop::class])->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * Shop back office under /admin.
     */
    protected function mapAdminRoutes(): void
    {
        Route::middleware(['web', ResolveShop::class, 'auth:shop'])
            ->prefix('admin')
            ->name('shop.admin.')
            ->group(module_path($this->name, '/routes/admin.php'));
    }

    /**
     * Customer storefront: home, store, product, cart, checkout, customer accounts.
     */
    protected function mapStorefrontRoutes(): void
    {
        Route::middleware(['web', ResolveShop::class])->group(module_path($this->name, '/routes/storefront.php'));
    }
}
