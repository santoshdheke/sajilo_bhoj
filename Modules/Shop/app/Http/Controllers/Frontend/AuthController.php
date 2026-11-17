<?php

namespace Modules\Shop\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Shop\Models\Customer;
use Modules\Shop\Support\Storefront;
use Modules\Shop\Support\ShopTenancy;

/**
 * Storefront customer accounts (guard "shop_customer"). A counter (POS) customer without a
 * password can claim their record by registering with the same email or phone number.
 */
class AuthController extends Controller
{
    private const VIEW = 'auth.';

    public function loginPage(): View|RedirectResponse
    {
        return Auth::guard('shop_customer')->check()
            ? redirect()->route('shop.front.account.dashboard')
            : view(Storefront::view(self::VIEW.'login'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        $credentials['company_id'] = ShopTenancy::id();

        if (! Auth::guard('shop_customer')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->with('error', 'Email or password does not match.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('shop.front.account.dashboard'));
    }

    public function registerPage(): View|RedirectResponse
    {
        return Auth::guard('shop_customer')->check()
            ? redirect()->route('shop.front.account.dashboard')
            : view(Storefront::view(self::VIEW.'register'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'email'        => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:255',
            'password'     => 'required|string|min:6|confirmed',
        ]);

        $existing = Customer::where('email', $data['email'])
            ->when($data['phone_number'] ?? null, fn ($q, $phone) => $q->orWhere('phone_number', $phone))
            ->first();

        if ($existing?->password) {
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'An account with this email or phone already exists. Please sign in.');
        }

        $fields = [
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'] ?? null,
            'email'        => $data['email'],
            'phone_number' => $data['phone_number'] ?? $existing?->phone_number,
            'address_one'  => $data['address'] ?? $existing?->address_one,
            'password'     => $data['password'],
        ];

        $customer = $existing ? tap($existing)->update($fields) : Customer::create($fields);

        Auth::guard('shop_customer')->login($customer, true);
        $request->session()->regenerate();

        return redirect()->route('shop.front.account.dashboard')->with('success', 'Welcome! Your account is ready.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('shop_customer')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('shop.front.home');
    }
}
