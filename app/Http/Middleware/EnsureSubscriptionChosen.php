<?php

namespace App\Http\Middleware;

use App\Support\Tenancy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks access to the dashboard until the tenant has picked a plan.
 * Excluded from the subscription.choose/store and logout routes.
 */
class EnsureSubscriptionChosen
{
    public function handle(Request $request, Closure $next): Response
    {
        $company = Tenancy::company();

        if ($company && ! $company->subscriptions()->exists()) {
            return redirect()->route('admin.subscription.choose');
        }

        return $next($request);
    }
}
