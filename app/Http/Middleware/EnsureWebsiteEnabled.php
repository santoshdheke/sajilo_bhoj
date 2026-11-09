<?php

namespace App\Http\Middleware;

use App\Support\Tenancy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The public website (menu, online ordering, QR menu, bookings, customer accounts)
 * is a plan feature. When the restaurant's plan doesn't include it, visitors see
 * a friendly "coming soon" page. The admin panel is not affected.
 */
class EnsureWebsiteEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $plan = Tenancy::company()?->currentPlan();

        if ($plan && $request->routeIs('frontend.*') && ! $plan->hasFeature('website')) {
            return response()->view('errors.website-off', [
                'name'  => \App\Models\Setting::getValue(Tenancy::company()->id, 'restaurant_name') ?: Tenancy::company()->name,
                'color' => \App\Models\Setting::getValue(Tenancy::company()->id, 'theme_color') ?: '#198754',
            ], 503);
        }

        return $next($request);
    }
}
