<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Http\Controllers\Admin\SettingController;
use App\Models\Domain;
use App\Models\NavMenuItem;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Support\DefaultKitchen;
use App\Support\DefaultMenu;
use App\Support\DefaultRoles;
use App\Support\DefaultTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        return view('admin.theme_one.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $domain = $request->getHost();

        $existing = Domain::with('company')->where('domain', $domain)->first();
        if ($existing?->company && $existing->company->isVerified()) {
            throw ValidationException::withMessages([
                'company_name' => "A workspace already exists for {$domain}.",
            ]);
        }
        // Someone started a sign-up here but never verified the email: start over cleanly.
        if ($existing) {
            DB::transaction(fn () => $existing->company ? $existing->company->purgeUnverified() : $existing->delete());
        }

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'theme_color'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|max:255|unique:companies,email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'owner_email.unique' => 'This email is already used by another workspace.',
        ]);

        $owner = DB::transaction(function () use ($data, $domain) {
            $company = Company::create([
                'name'   => $data['company_name'],
                'slug'   => Str::slug($data['company_name']).'-'.Str::lower(Str::random(5)),
                'email'  => $data['owner_email'],
                'status' => true,
            ]);

            $branch = Branch::create([
                'company_id' => $company->id,
                'name'       => 'Main Branch',
                'email'      => $data['owner_email'],
                'status'     => true,
            ]);

            Domain::create([
                'company_id' => $company->id,
                'domain'     => $domain,
                'is_primary' => true,
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
        return redirect()
            ->route('admin.verify_email.show')
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
