<?php

namespace Modules\School\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\School\Models\User;
use Modules\School\Services\ActivityLog\ActivityLogService;
use Symfony\Component\HttpFoundation\Response;

/**
 * sms's RoleMiddleware ("role:admin" etc.): 403 unless the signed-in school user has one of
 * the roles, and the denial is written to the activity log.
 */
class EnsureRole
{
    public function __construct(private readonly ActivityLogService $activityLog) {}

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasRole(...$roles)) {
            $this->activityLog->log($user, 'auth.role_denied', properties: [
                'required_roles' => $roles,
                'path'           => $request->path(),
            ]);

            abort(403, 'You do not have access to this resource.');
        }

        return $next($request);
    }
}
