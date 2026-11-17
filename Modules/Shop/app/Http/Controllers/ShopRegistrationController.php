<?php

namespace Modules\Shop\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Support\Databases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Shop\Mail\ShopOtpMail;
use Modules\Shop\Models\Company;
use Modules\Shop\Models\ShopSetting;
use Modules\Shop\Models\User;
use Modules\Shop\Support\DefaultCatalog;

/**
 * Shop sign-up (multiple_pasal's AuthController::companyRegister + email OTP): the owner verifies
 * their email with a 6-digit code first, then the shop (company + domain in the common database,
 * owner + starter catalog in the shop database) is created on the current host.
 */
class ShopRegistrationController extends Controller
{
    private const OTP_MINUTES = 10;

    public function create(Request $request): View
    {
        return view('shop::auth.register', ['host' => $request->getHost()]);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|max:255']);

        if (Company::withoutGlobalScopes()->where('email', $request->email)->exists()) {
            return response()->json(['success' => false, 'message' => 'This email is already registered to another workspace.'], 422);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'shop_otp'            => $otp,
            'shop_otp_email'      => $request->email,
            'shop_otp_expires_at' => now()->addMinutes(self::OTP_MINUTES)->timestamp,
        ]);
        session()->forget('shop_otp_verified_email');

        try {
            Mail::to($request->email)->send(new ShopOtpMail($otp, self::OTP_MINUTES));
        } catch (\Throwable $e) {
            Log::error('ShopOtpMail failed', ['email' => $request->email, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'We could not send the email. Please check the address and try again.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'A 6-digit code has been sent to '.$request->email]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|string|size:6']);

        $storedOtp   = session('shop_otp');
        $storedEmail = session('shop_otp_email');
        $expiresAt   = session('shop_otp_expires_at');

        $error = match (true) {
            ! $storedOtp || ! $storedEmail || ! $expiresAt => 'No code found. Please request a new code.',
            now()->timestamp > $expiresAt                  => 'The code has expired. Please request a new code.',
            $request->email !== $storedEmail               => 'This email does not match the one the code was sent to.',
            ! hash_equals($storedOtp, $request->otp)       => 'Incorrect code. Please try again.',
            default                                        => null,
        };

        if ($error) {
            return response()->json(['success' => false, 'message' => $error], 422);
        }

        session(['shop_otp_verified_email' => $request->email]);

        return response()->json(['success' => true, 'message' => 'Email verified.']);
    }

    public function store(Request $request): RedirectResponse
    {
        $host = $request->getHost();

        // The domains table is shared by restaurants and shops: one workspace per host.
        $existing = Domain::with('company')->where('domain', $host)->first();
        if ($existing?->company) {
            throw ValidationException::withMessages(['shop_name' => "A workspace already exists for {$host}."]);
        }

        $data = $request->validate([
            'shop_name'    => 'required|string|max:255',
            'owner_name'   => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:companies,email',
            'phone_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'address'      => 'nullable|string|max:255',
            'password'     => 'required|string|min:8|confirmed',
            'theme_color'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'email.unique'       => 'This email is already used by another workspace.',
            'phone_number.regex' => 'Enter a 10-digit phone number.',
        ]);

        if (session('shop_otp_verified_email') !== $data['email']) {
            throw ValidationException::withMessages(['email' => 'Please verify this email address with the code we send you.']);
        }

        $existing?->delete(); // a domain row left without a company

        $owner = Databases::createCompany(Databases::SHOP, function () use ($data, $host) {
            $company = Company::create([
                'name'    => $data['shop_name'],
                'slug'    => Str::slug($data['shop_name']).'-'.Str::lower(Str::random(5)),
                'email'   => $data['email'],
                'phone'   => $data['phone_number'],
                'address' => $data['address'] ?? null,
                'status'  => true,
            ]);

            Domain::create(['company_id' => $company->id, 'domain' => $host, 'is_primary' => true]);

            return $company;
        }, function ($company) use ($data) {
            $owner = User::create([
                'company_id'        => $company->id,
                'name'              => $data['owner_name'],
                'email'             => $data['email'],
                'phone_number'      => $data['phone_number'],
                'address'           => $data['address'] ?? null,
                'password'          => $data['password'],
                'role'              => User::ROLE_OWNER,
                'status'            => true,
                'email_verified_at' => now(), // verified by the code above
            ]);

            DefaultCatalog::seed($company->id);

            // Theme colour chosen on the sign-up page (storefront + back office). Stored without "#".
            ShopSetting::create([
                'company_id' => $company->id,
                'logo_color' => strtolower(ltrim($data['theme_color'] ?? '#f97316', '#')),
            ]);

            return $owner;
        });

        Auth::guard('shop')->login($owner, true);
        $request->session()->regenerate();
        session()->forget(['shop_otp', 'shop_otp_email', 'shop_otp_expires_at', 'shop_otp_verified_email']);

        // A path, not a route name: only the sign-up routes are loaded until this host is a shop domain.
        return redirect('/admin/dashboard')->with('success', 'Your shop is ready! Add your products to get started.');
    }
}
