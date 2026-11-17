<?php

namespace Modules\Restaurant\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Domain as BaseDomain;
use App\Support\Databases;

use Modules\Restaurant\Models\Branch;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Http\Controllers\Admin\SettingController;
use Modules\Restaurant\Models\Domain;
use Modules\Restaurant\Models\NavMenuItem;
use Modules\Restaurant\Models\Role;
use Modules\Restaurant\Models\Setting;
use Modules\Restaurant\Models\User;
use Modules\Restaurant\Support\DefaultKitchen;
use Modules\Restaurant\Support\DefaultMenu;
use Modules\Restaurant\Support\DefaultRoles;
use Modules\Restaurant\Support\DefaultTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $owner = Databases::createCompany(Databases::RESTAURANT, function () use ($data, $domain) {
            $company = Company::create([
                'name'   => $data['company_name'],
                'slug'   => Str::slug($data['company_name']).'-'.Str::lower(Str::random(5)),
                'email'  => $data['owner_email'],
                'status' => true,
            ]);

            Domain::create([
                'company_id' => $company->id,
                'domain'     => $domain,
                'is_primary' => true,
            ]);

            return $company;
        }, function (Company $company) use ($data) {
            $branch = Branch::create([
                'company_id' => $company->id,
                'name'       => 'Main Branch',
                'email'      => $data['owner_email'],
                'status'     => true,
            ]);

            $this->seedDefaults($company, $data);
            DefaultMenu::seed($branch);
            DefaultTables::seed($branch);
            DefaultKitchen::seed($branch);

            $role = Role::create([
                'company_id'  => $company->id,
                'name'        => 'Owner',
                'description' => 'Full access to every module.',
                'permissions' => ['*'],
            ]);
            DefaultRoles::seed($company);

            return User::create([
                'company_id' => $company->id,
                'branch_id'  => $branch->id,
                'role_id'    => $role->id,
                'name'       => $data['owner_name'],
                'email'      => $data['owner_email'],
                'password'   => Hash::make($data['password']),
                'is_active'  => true,
            ]);
        });

        Auth::guard('web')->login($owner);
        $request->session()->regenerate();

        // The workspace goes live once the owner verifies their email (code is sent on that screen).
        // A path, not a route name: sign-up runs before the host is a restaurant domain, when only the
        // sign-up routes are loaded (Modules\Restaurant\Providers\RouteServiceProvider).
        return redirect('/admin/verify-email')
            ->with('status', "Almost done! We sent a code to {$data['owner_email']}.");
    }

    /**
     * Give a new company a complete set of website settings (every settings section),
     * personalised with its name, email and chosen brand colour, plus the default
     * navigation menu — so the storefront looks finished from the first visit.
     * The starter food menu is seeded separately by DefaultMenu.
     */
    private function seedDefaults(Company $company, array $data): void
    {
        $name = $data['company_name'];

        $values = array_merge(SettingController::allDefaults(), [
            'restaurant_name' => $name,
            'tagline'         => 'Fresh taste, warm service, elegant dining.',
            'theme_color'     => $data['theme_color'] ?? '#198754',
            'contact_email'   => $data['owner_email'],
            'opening_hours'   => "Sunday - Friday: 10:00 AM - 10:00 PM\nSaturday: 11:00 AM - 11:00 PM",
            'about_text'      => "{$name} brings people together over honest, freshly prepared food. "
                               . 'From our kitchen to your table, every dish is cooked with care, served with a smile '
                               . 'and priced to keep you coming back.',
        ]);

        foreach ($values as $key => $value) {
            Setting::putValue($company->id, $key, $value);
        }

        NavMenuItem::forCompany($company->id);
    }
}
