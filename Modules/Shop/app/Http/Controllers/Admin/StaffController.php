<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Mail\StaffInviteMail;
use Modules\Shop\Models\User;
use Modules\Shop\Support\ShopTenancy;

/**
 * Shop staff (multiple_pasal's StaffController): new staff start inactive without a password;
 * the owner sends an invite and the staff member chooses their own password from the link.
 */
class StaffController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.staff.index', [
            'staff' => User::orderByRaw("role = 'owner' DESC")->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        User::create($data + [
            'role'         => User::ROLE_STAFF,
            'status'       => false,
            'invite_token' => Str::random(60),
        ]);

        return back()->with('success', 'Staff member added. Send the invite so they can set their password.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        $extra = $request->validate([
            'status'   => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // The owner always stays active; you can't lock yourself out either.
        if (! $user->isOwner() && $user->id !== $request->user('shop')->id) {
            $data['status'] = $request->boolean('status');
        }
        if (! empty($extra['password'])) {
            $data['password'] = $extra['password'];
            $data['email_verified_at'] = $user->email_verified_at ?? now();
            $data['invite_token'] = null;
        }

        $user->update($data);

        return back()->with('success', 'Staff member updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->isOwner() || $user->id === $request->user('shop')->id) {
            return back()->with('error', 'The shop owner and your own account can\'t be deleted.');
        }

        $user->delete();

        return back()->with('success', 'Staff member deleted.');
    }

    public function sendInvite(User $user): RedirectResponse
    {
        if ($user->isOwner()) {
            return back()->with('error', 'The owner doesn\'t need an invite.');
        }

        $user->update(['invite_token' => Str::random(60)]);
        $link = route('shop.staff.set_password', $user->invite_token);

        try {
            Mail::to($user->email)->send(new StaffInviteMail($user->name, ShopTenancy::company()->name, $link));
        } catch (\Throwable $e) {
            Log::error('StaffInviteMail failed', ['user' => $user->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'The invite email could not be sent. Check the email address and mail settings.');
        }

        return back()->with('success', "Invite sent to {$user->email}.");
    }

    // ── Public (invite link) ──

    public function setPasswordForm(string $token): View
    {
        $user = User::where('invite_token', $token)->firstOrFail();

        return view('shop::auth.set-password', ['user' => $user, 'token' => $token, 'shopName' => ShopTenancy::company()->name]);
    }

    public function setPassword(Request $request, string $token): RedirectResponse
    {
        $user = User::where('invite_token', $token)->firstOrFail();

        $request->validate(['password' => 'required|string|min:8|confirmed']);

        $user->update([
            'password'          => $request->password,
            'status'            => true,
            'email_verified_at' => now(),
            'invite_token'      => null,
        ]);

        return redirect()->route('shop.admin.auth.show_login_form')->with('success', 'Password set. You can sign in now.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => ['required', 'email', 'max:255', Rule::unique('shop.users', 'email')->where('company_id', ShopTenancy::id())->ignore($user?->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string', 'max:255'],
        ]);
    }
}
