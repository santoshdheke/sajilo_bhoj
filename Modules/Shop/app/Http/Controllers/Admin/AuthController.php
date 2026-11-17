<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Shop\Support\ShopTenancy;

/** Shop back-office login (guard "shop"), limited to the accounts of this shop. */
class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('shop')->check()) {
            return redirect()->route('shop.admin.dashboard.index');
        }

        return view('shop::auth.login', ['shopName' => ShopTenancy::company()?->name]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials['company_id'] = ShopTenancy::id();

        if (! Auth::guard('shop')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email or password does not match.']);
        }

        $user = Auth::guard('shop')->user();
        if (! $user->status || ! $user->email_verified_at) {
            Auth::guard('shop')->logout();
            throw ValidationException::withMessages(['email' => $user->email_verified_at
                ? 'Your account is inactive. Please contact the shop owner.'
                : 'Please set your password from the invite email first.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('shop.admin.dashboard.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('shop')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.admin.auth.show_login_form');
    }
}
