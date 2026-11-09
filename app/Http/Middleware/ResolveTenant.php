<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active Company (and Branch) for the request based on the host,
 * matching it against the `domains` table, and binds them into the container
 * as 'tenant' / 'tenant_branch' for the BelongsToCompany/BelongsToBranch scopes
 * (see App\Support\Tenancy) to pick up.
 *
 * Hosts that don't match any tenant domain are treated as the central/marketing
 * domain (sign-up, company directory) — no tenant is bound there.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // The "get started" sign-up flow must stay reachable from a domain that
        // has no tenant yet (that's exactly where the 410 page sends people),
        // so let it through before any tenant lookup happens.
        if ($request->routeIs('register.*')) {
            return $next($request);
        }

        $host = $request->getHost();

        $domain = Domain::with('company')->where('domain', $host)->first();
        if(!isset($domain)){
            abort(410, $host);
        }

        // Signed up but the owner never verified the email: the workspace doesn't exist yet,
        // so show the "Create your workspace" (410) page again. Only the owner's own
        // verify-code screen (and logout) stay reachable, to finish the sign-up they started.
        if ($domain->company && ! $domain->company->isVerified()
            && ! $request->routeIs('admin.verify_email.*', 'admin.logout')) {
            abort(410, $host);
        }

        if ($domain && $domain->company && $domain->company->status) {
            App::instance('tenant', $domain->company);

            if ($branch = $this->resolveBranch($request, $domain->company)) {
                App::instance('tenant_branch', $branch);
            }

            return $next($request);
        }

        if ($this->isCentralDomain($host)) {
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
