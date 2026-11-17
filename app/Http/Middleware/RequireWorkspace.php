<?php

namespace App\Http\Middleware;

use App\Support\Workspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Web requests on a host without a usable workspace (no company yet, or a sign-up whose
 * owner never verified the email) only see the 410 "create your workspace" page at "/".
 * Any other URL is sent to "/". The sign-up pages, and the verify-email screen of an
 * unfinished sign-up, stay reachable (registered by the modules on Workspace).
 */
class RequireWorkspace
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs(...Workspace::routesWithoutWorkspace())) {
            return $next($request);
        }

        $company = Workspace::company();

        if (! $company || (! $company->isVerified() && ! $request->routeIs(...Workspace::routesWhileUnverified()))) {
            return self::noWorkspace($request);
        }

        return $next($request);
    }

    /** The 410 page lives only at "/", so any other URL (e.g. /admin/login, /menu) is sent there. */
    public static function noWorkspace(Request $request): Response
    {
        if ($request->path() !== '/') {
            return redirect('/');
        }

        abort(410, $request->getHost());
    }
}
