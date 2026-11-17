<?php

namespace Modules\School\Providers;

use App\Models\Company as BaseCompany;
use App\Support\Workspace;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Session\Middleware\AuthenticateSession;
use Modules\School\Enums\UserRoleEnum;
use Modules\School\Models\User;
use Nwidart\Modules\Support\ModuleServiceProvider;

/**
 * School management system (ported from the "sms" project's Admin/Teacher/Student role
 * modules): its own database ("school" connection), its own login guard ("school"), and
 * routes only on school domains (see RouteServiceProvider).
 *
 * In sms each role was a module; here they are areas of this one module:
 * /admin (role admin), /teacher (role teacher), /student (role student), plus the shared /login.
 * The sms Superadmin module has no counterpart: the platform-level tenant is App\Models\Company.
 */
class SchoolServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'School';

    protected string $nameLower = 'school';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        // School people log in with their own guard, so a restaurant/shop session never mixes in.
        config([
            'auth.guards.school'           => ['driver' => 'session', 'provider' => 'school_users'],
            'auth.providers.school_users'  => ['driver' => 'eloquent', 'model' => User::class],
        ]);
    }

    public function boot(): void
    {
        parent::boot();

        $this->registerWorkspace();
        $this->registerGuestRedirect();
    }

    /**
     * How the shared workspace logic treats schools: the sign-up page works on hosts without a
     * company; a school is live once it has an admin (sign-up creates both together).
     */
    private function registerWorkspace(): void
    {
        BaseCompany::verifyUsing(BaseCompany::TYPE_SCHOOL, fn (BaseCompany $company) => User::query()
            ->where('company_id', $company->id)->where('role', UserRoleEnum::Admin->value)->exists());

        Workspace::allowWithoutWorkspace(['school.register.*']);
    }

    /** Guests on a school domain go to the school login (guest redirects are global, so only on school hosts). */
    private function registerGuestRedirect(): void
    {
        if ($this->app->runningInConsole() || Workspace::type() !== BaseCompany::TYPE_SCHOOL) {
            return;
        }

        $toLogin = fn () => route('school.login');

        Authenticate::redirectUsing($toLogin);
        AuthenticateSession::redirectUsing($toLogin);
        AuthenticationException::redirectUsing($toLogin);
    }
}
