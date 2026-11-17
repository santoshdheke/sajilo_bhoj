<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\ShopSetting;
use Modules\Shop\Support\ShopTenancy;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        return view('shop::admin.theme_one.profile.index', ['user' => $request->user('shop')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user('shop');

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => ['required', 'email', 'max:255', Rule::unique('shop.users', 'email')->where('company_id', ShopTenancy::id())->ignore($user->id)],
            'phone_number'    => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('shop/'.ShopTenancy::id().'/avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('shop.admin.profile.index')->with('success', 'Profile updated.');
    }

    /** Theme colour of the shop (storefront + back office), same value as Settings → Branding. */
    public function updateTheme(Request $request): RedirectResponse
    {
        $data = $request->validate(['theme_color' => ['required', 'regex:/^#?[0-9A-Fa-f]{6}$/']]);

        ShopSetting::current()->update(['logo_color' => strtolower(ltrim($data['theme_color'], '#'))]);

        return redirect()->route('shop.admin.profile.index')->with('success', 'Theme colour updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user('shop');

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => $request->password]);

        return redirect()->route('shop.admin.profile.index')->with('success', 'Password changed.');
    }
}
