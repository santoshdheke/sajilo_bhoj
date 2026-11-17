<?php

namespace Modules\School\Providers;

use App\Models\Company;
use App\Support\Workspace;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\School\Http\Middleware\EnsureRole;
use Modules\School\Http\Middleware\ResolveSchool;

/**
 * School routes are only registered for school domains (company_type = school), so a
 * restaurant or shop domain can use the same URLs. The sign-up page is also there on hosts
 * without a company.
 *
 * Like sms, this provider owns prefix + auth + role; the route files stay plain.
 */
class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'School';

    public function boot(): void
    {
        parent::boot();

        // The school (and its guard) must be known before the logged-in user loads.
        $this->app->make(HttpKernel::class)->prependToMiddlewarePriority(ResolveSchool::class);
    }

    public function map(): void
    {
        if (Workspace::serves(Company::TYPE_SCHOOL) || Workspace::isUnclaimedHost()) {
            $this->mapRegisterRoutes();
        }

        if (Workspace::serves(Company::TYPE_SCHOOL)) {
            $this->mapWebRoutes();
            $this->mapRoleRoutes('admin', 'admin');
            $this->mapRoleRoutes('teacher', 'teacher');
            $this->mapRoleRoutes('student', 'student');
        }
    }

    /**
     * School sign-up (/school/register).
     */
    protected function mapRegisterRoutes(): void
    {
        Route::middleware('web')->group(module_path($this->name, '/routes/register.php'));
    }

    /**
     * Shared by every school person: "/", login, logout.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', ResolveSchool::class])->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * One area per role (sms had one module per role): /admin, /teacher, /student.
     */
    protected function mapRoleRoutes(string $area, string $role): void
    {
        Route::middleware(['web', ResolveSchool::class, 'auth:school', EnsureRole::class.':'.$role])
            ->prefix($area)
            ->name("school.{$area}.")
            ->group(module_path($this->name, "/routes/{$area}.php"));
    }
}
