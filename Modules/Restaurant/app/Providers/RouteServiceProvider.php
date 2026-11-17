<?php

namespace Modules\Restaurant\Providers;

use App\Models\Company;
use App\Support\Workspace;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Restaurant\Http\Middleware\EnsureOwnerEmailVerified;
use Modules\Restaurant\Http\Middleware\EnsureSubscriptionActive;
use Modules\Restaurant\Http\Middleware\EnsureSubscriptionChosen;
use Modules\Restaurant\Http\Middleware\EnsureWebsiteEnabled;
use Modules\Restaurant\Http\Middleware\ResolveTenant;

/**
 * Restaurant routes are only registered for restaurant domains (company_type = restaurant),
 * so a shop domain can use the same URLs. The sign-up page is also there on hosts without a company.
 */
class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Restaurant';

    public function map(): void
    {
        if (Workspace::serves(Company::TYPE_RESTAURANT) || Workspace::isUnclaimedHost()) {
            $this->mapRegisterRoutes();
        }

        if (Workspace::serves(Company::TYPE_RESTAURANT)) {
            $this->mapWebRoutes();
            $this->mapAdminRoutes();
        }
    }

    /**
     * Restaurant sign-up (/restaurant/register).
     */
    protected function mapRegisterRoutes(): void
    {
        Route::middleware('web')->group(module_path($this->name, '/routes/register.php'));
    }

    /**
     * Public website, customer accounts and admin login.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', ResolveTenant::class, EnsureWebsiteEnabled::class])
            ->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * Restaurant back office under /admin.
     */
    protected function mapAdminRoutes(): void
    {
        Route::middleware(['web', ResolveTenant::class, EnsureWebsiteEnabled::class, 'auth:web', EnsureOwnerEmailVerified::class, EnsureSubscriptionChosen::class, EnsureSubscriptionActive::class])
            ->prefix('admin')
            ->name('admin.')
            ->group(module_path($this->name, '/routes/admin.php'));
    }
}
