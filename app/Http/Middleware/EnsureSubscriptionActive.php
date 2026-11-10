<?php

namespace App\Http\Middleware;

use App\Support\Billing;
use App\Support\Tenancy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the subscription billed and stops the admin panel when it has run out:
 * owners/managers go to the top-up page, other staff see a short notice.
 */
class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $company = Tenancy::company();
        $sub = $company ? Billing::settleSoon($company) : null;

        if ($sub?->isExpired()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your subscription has run out. Please top up to continue.'], 402);
            }

            return $request->user()?->hasPermission('users.manage')
                ? redirect()->route('admin.subscription.topup')
                : response()->view('admin.theme_one.subscription.expired', [], 402);
        }

        return $next($request);
    }
}
