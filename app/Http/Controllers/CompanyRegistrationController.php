<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Domain;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
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
 * tenant (Company + default Branch + Domain + Owner role + owner User) in one
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

        if (Domain::where('domain', $domain)->exists()) {
            throw ValidationException::withMessages([
                'company_name' => "A workspace already exists for {$domain}.",
            ]);
        }

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'theme_color'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|max:255|unique:companies,email',
            'password'     => 'required|string|min:8|confirmed',
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

            Setting::putValue($company->id, 'theme_color', $data['theme_color'] ?? '#198754');

            $role = Role::create([
                'company_id'  => $company->id,
                'name'        => 'Owner',
                'description' => 'Full access to every module.',
                'permissions' => ['*'],
            ]);

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

        return redirect()
            ->route('admin.dashboard.index')
            ->with('status', "Workspace \"{$data['company_name']}\" is ready.");
    }
}
