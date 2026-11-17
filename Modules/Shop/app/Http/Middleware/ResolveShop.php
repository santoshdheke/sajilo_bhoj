<?php

namespace Modules\Shop\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Modules\Shop\Models\Company;
use Modules\Shop\Support\ShopTenancy;
use Symfony\Component\HttpFoundation\Response;

/**
 * Binds the shop company of this host (multiple_pasal's CompanyMiddleware). Only shop domains
 * get shop routes; hosts without a workspace were already handled by App\Http\Middleware\RequireWorkspace.
 */
class ResolveShop
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain  = Domain::where('domain', $request->getHost())->first();
        $company = $domain ? Company::find($domain->company_id) : null;

        if (! $company || ! $company->status) {
            abort(404, 'No shop is registered at this address.');
        }

        app()->instance(ShopTenancy::BINDING, $company);

        return $next($request);
    }
}
