<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\PlanLimits;
use App\Support\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('frontend.home.index');
        }

        return view('frontend.theme_one.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('frontend.home.index'));
    }

    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('frontend.home.index');
        }

        return view('frontend.theme_one.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $company = Tenancy::company();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'email', 'max:255',
                Rule::unique('customers', 'email')->where('company_id', $company?->id),
            ],
            'phone'    => 'nullable|string|max:50',
            'address'  => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (! PlanLimits::canAdd($company, 'customers')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Sorry, we can’t create new accounts right now. You can still order as a guest.',
            ]);
        }

        $customer = Customer::create([
            'company_id' => $company?->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'address'    => $data['address'] ?? null,
            'password'   => Hash::make($data['password']),
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('frontend.home.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home.index');
    }
}
