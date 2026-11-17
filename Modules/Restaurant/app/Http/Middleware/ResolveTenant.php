<?php

namespace Modules\Restaurant\Http\Middleware;

use Modules\Restaurant\Models\Branch;
use Modules\Restaurant\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active Company (and Branch) for the request based on the host,
 * matching it against the `domains` table, and binds them into the container
 * as 'tenant' / 'tenant_branch' for the BelongsToCompany/BelongsToBranch scopes
 * (see Modules\Restaurant\Support\Tenancy) to pick up.
 *
 * Only restaurant domains reach it (Modules\Restaurant\Providers\RouteServiceProvider);
 * hosts without a verified workspace are handled earlier by App\Http\Middleware\RequireWorkspace.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hosts without a (verified) workspace never get here with a tenant to bind:
        // App\Http\Middleware\RequireWorkspace already sent them to the 410 page.
        $domain = Domain::with('company')->where('domain', $request->getHost())->first();

        if ($domain?->company && $domain->company->status) {
            App::instance('tenant', $domain->company);

            if ($branch = $this->resolveBranch($request, $domain->company)) {
                App::instance('tenant_branch', $branch);
            }

            return $next($request);
        }

        if ($this->isCentralDomain($request->getHost())) {
            return $next($request);
        }

        abort(404, 'No restaurant is registered at this address.');
    }

    private function isCentralDomain(string $host): bool
    {
        $central = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';

        return in_array($host, [$central, 'localhost', '127.0.0.1'], true);
    }

    private function resolveBranch(Request $request, $company): ?Branch
    {
        $user = $request->user('web');

        if ($user && $user->branch) {
            return $user->branch;
        }

        if ($branchId = $request->session()->get('storefront_branch_id')) {
            $branch = Branch::withoutGlobalScope('company')
                ->where('company_id', $company->id)
                ->find($branchId);

            if ($branch) {
                return $branch;
            }
        }

        return $company->branches()->where('status', true)->first();
    }
}
