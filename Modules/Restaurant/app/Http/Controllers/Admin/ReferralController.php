<?php

namespace Modules\Restaurant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Models\WalletTransaction;
use Modules\Restaurant\Support\Billing;
use Modules\Restaurant\Support\RestaurantSignup;
use Modules\Restaurant\Support\Tenancy;

/**
 * Refer & Earn: add client restaurants and earn a bonus (billing.referral_bonus_percent)
 * on your own balance every time one of them tops up.
 */
class ReferralController extends Controller
{
    public function index(Request $request): View
    {
        $company = Tenancy::company();

        $clients = Company::where('referred_by_company_id', $company->id)
            ->with(['domains' => fn ($q) => $q->orderByDesc('is_primary')])
            ->latest('id')
            ->paginate(10);

        // Bonus earned from each client, and in total.
        $earned = WalletTransaction::where('type', 'bonus')
            ->selectRaw('source_company_id, SUM(amount) AS total, COUNT(*) AS payments')
            ->groupBy('source_company_id')
            ->get()
            ->keyBy('source_company_id');

        return view('restaurant::admin.theme_one.subscription.referrals', [
            'clients'      => $clients,
            'earned'       => $earned,
            'totalEarned'  => (float) $earned->sum('total'),
            'clientCount'  => $clients->total(),
            'payingCount'  => $earned->whereNotNull('source_company_id')->count(),
            'percent'      => Billing::referralPercent(),
            'domainSuffix' => $this->domainSuffix($request->getHost()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['domain' => Str::lower(trim((string) $request->input('domain'), " \t\n\r\0\x0B/"))]);
        $request->merge(['domain' => preg_replace('#^https?://#', '', $request->input('domain'))]);

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'domain'       => ['required', 'string', 'max:253', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/', 'unique:domains,domain'],
            'theme_color'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|max:255|unique:companies,email|unique:restaurant.users,email',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'domain.regex'       => 'Enter a web address like newplace.example.com (no http:// or slashes).',
            'domain.unique'      => 'This web address is already used by another workspace.',
            'owner_email.unique' => 'This email is already used by another workspace.',
        ], [
            'company_name' => 'restaurant name',
            'domain'       => 'web address',
        ]);

        RestaurantSignup::create($data, $data['domain'], Tenancy::company());

        return redirect()->route('admin.subscription.referrals.index')
            ->with('status', "{$data['company_name']} is ready. Its owner can sign in at {$data['domain']}/admin/login with {$data['owner_email']}.");
    }

    /** ".example.com" from "first.example.com", to suggest a client address on the same domain. */
    private function domainSuffix(string $host): ?string
    {
        $parts = explode('.', $host);

        return count($parts) > 2 ? '.' . implode('.', array_slice($parts, 1)) : null;
    }
}
