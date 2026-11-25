<?php

namespace Modules\Restaurant\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Domain as BaseDomain;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Support\RestaurantSignup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Public sign-up flow served on the central/marketing domain — creates a new
 * tenant (Company + default Branch + Domain + Owner role + owner User, plus a
 * starter menu, Table 1–4 and kitchen columns) in one
 * transaction so the new restaurant can immediately sign in on its own domain.
 */
class CompanyRegistrationController extends Controller
{
    public function create(Request $request): View
    {
        return view('restaurant::admin.theme_one.auth.register', [
            'host' => $request->getHost(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $domain = $request->getHost();

        // Look at the domain across all company types (restaurant and shop share the domains table).
        $existing = BaseDomain::with('company')->where('domain', $domain)->first();
        if ($existing?->company && ($existing->company->isVerified() || $existing->company->company_type !== Company::TYPE_RESTAURANT)) {
            throw ValidationException::withMessages([
                'company_name' => "A workspace already exists for {$domain}.",
            ]);
        }
        // Someone started a restaurant sign-up here but never verified the email: start over cleanly.
        if ($existing) {
            $existing->company
                ? Company::find($existing->company_id)->purgeUnverified()
                : $existing->delete();
        }

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'theme_color'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|max:255|unique:companies,email|unique:restaurant.users,email',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'owner_email.unique' => 'This email is already used by another workspace.',
        ]);

        // Company + domain (common database) first, then the restaurant data (restaurant database).
        $owner = RestaurantSignup::create($data, $domain);

        Auth::guard('web')->login($owner);
        $request->session()->regenerate();

        // The workspace goes live once the owner verifies their email (code is sent on that screen).
        // A path, not a route name: sign-up runs before the host is a restaurant domain, when only the
        // sign-up routes are loaded (Modules\Restaurant\Providers\RouteServiceProvider).
        return redirect('/admin/verify-email')
            ->with('status', "Almost done! We sent a code to {$data['owner_email']}.");
    }
}
