<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\EmailCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->email_verified_at) {
            return redirect()->route('admin.dashboard.index');
        }

        // First visit (or the old code expired): send one automatically.
        if (! EmailCode::hasActiveCode($user) && EmailCode::resendIn($user) === 0) {
            EmailCode::send($user);
        }

        return view('admin.theme_one.auth.verify-email', [
            'email'    => $user->email,
            'resendIn' => EmailCode::resendIn($user),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']], ['code.digits' => 'Enter the 6-digit code from the email.']);

        if ($error = EmailCode::check($request->user(), $request->code)) {
            return back()->withErrors(['code' => $error]);
        }

        $request->user()->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('admin.dashboard.index')->with('status', 'Email verified — welcome aboard!');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($wait = EmailCode::resendIn($user)) {
            return back()->withErrors(['code' => "Please wait {$wait} seconds before asking for a new code."]);
        }

        EmailCode::send($user);

        return back()->with('status', 'A new code is on its way.');
    }
}
