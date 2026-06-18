<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates an admin route behind one of the active user's role permissions.
 * Usage: ->middleware('permission:orders.manage')
 * or, for "any of": ->middleware('permission:orders.view,orders.manage')
 */
class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user('web');

        if (! $user) {
            abort(401);
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this section.');
    }
}
