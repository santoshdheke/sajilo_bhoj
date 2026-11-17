<?php

namespace Modules\School\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\School\Models\Company;
use Modules\School\Models\User;
use Modules\School\Services\ActivityLog\ActivityLogService;
use Modules\School\Support\SchoolTenancy;
use Symfony\Component\HttpFoundation\Response;

/**
 * Binds the school of this host (sms's ResolveTenantMiddleware, "tenant:school") and makes
 * "school" the default guard, so auth()->user() / $request->user() are school users here.
 * Hosts without a workspace were already handled by App\Http\Middleware\RequireWorkspace.
 */
class ResolveSchool
{
    public function __construct(private readonly ActivityLogService $activityLog) {}

    public function handle(Request $request, Closure $next): Response
    {
        $domain  = Domain::where('domain', $request->getHost())->first();
        $company = $domain ? Company::find($domain->company_id) : null;

        // An inactive school's host is a plain 404 (as in sms).
        if (! $company || ! $company->status) {
            abort(404, 'No school is registered at this address.');
        }

        app()->instance(SchoolTenancy::BINDING, $company);
        Auth::shouldUse('school');

        // A signed-in user must belong to this school (sms: "auth.tenant_mismatch").
        $user = Auth::guard('school')->user();
        if ($user instanceof User && (int) $user->company_id !== $company->id) {
            $this->activityLog->log($user, 'auth.tenant_mismatch', properties: [
                'company_id' => $company->id,
                'path'       => $request->path(),
            ]);

            abort(403, 'You do not have access to this school.');
        }

        return $next($request);
    }
}
