<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\ShopSetting;
use Modules\Shop\Support\ShopTenancy;

/**
 * Shop settings (multiple_pasal's admin Setting page): store details and logo, branding colours
 * and font (storefront + back office), store texts, QR / Khalti payments and social links.
 */
class SettingController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.setting.index', [
            'company'  => ShopTenancy::company(),
            'settings' => ShopSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $hex = ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'];

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:30',
            'address'          => 'nullable|string|max:255',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'remove_logo'      => 'nullable|boolean',
            'logo_color'       => $hex,
            'logo_color_two'   => $hex,
            'text_color'       => $hex,
            'font_family'      => ['nullable', Rule::in(ShopSetting::FONTS)],
            'announcement'     => 'nullable|string|max:255',
            'about_us'         => 'nullable|string|max:2000',
            'vat_number'       => 'nullable|string|max:50',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'receipt_footer'   => 'nullable|string|max:500',
            'esewa_id'         => 'nullable|string|max:100',
            'esewa_name'       => 'nullable|string|max:100',
            'khalti_id'        => 'nullable|string|max:100',
            'khalti_name'      => 'nullable|string|max:100',
            'khalti_secret_key' => 'nullable|string|max:255',
            'remove_khalti_key' => 'nullable|boolean',
            'khalti_env'       => 'nullable|in:test,live',
            'social'           => 'nullable|array',
            'social.*'         => 'nullable|url|max:255',
        ]);

        // Store details live on the company (common database).
        $company = ShopTenancy::company();
        $company->fill([
            'name'    => $data['name'],
            'email'   => $data['email'] ?? null,
            'phone'   => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
        if ($request->hasFile('logo') || $request->boolean('remove_logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->hasFile('logo') ? $request->file('logo')->store('shop/'.$company->id.'/logo', 'public') : null;
        }
        $company->save();

        // Storefront settings (shop database). Colours are kept without "#", as the theme expects.
        $settings = ShopSetting::current();
        $color    = fn (?string $v, string $default) => strtolower(ltrim($v ?: $default, '#'));

        $settings->fill([
            'logo_color'       => $color($data['logo_color'] ?? null, $settings->logo_color),
            'logo_color_two'   => $color($data['logo_color_two'] ?? null, $settings->logo_color_two),
            'text_color'       => $color($data['text_color'] ?? null, $settings->text_color),
            'font_family'      => $data['font_family'] ?? $settings->font_family,
            'announcement'     => $data['announcement'] ?? null,
            'about_us'         => $data['about_us'] ?? null,
            'vat_number'       => $data['vat_number'] ?? null,
            'default_tax_rate' => $data['default_tax_rate'] ?? 0,
            'receipt_footer'   => $data['receipt_footer'] ?? null,
            'esewa_id'         => $data['esewa_id'] ?? null,
            'esewa_name'       => $data['esewa_name'] ?? null,
            'khalti_id'        => $data['khalti_id'] ?? null,
            'khalti_name'      => $data['khalti_name'] ?? null,
            'khalti_env'       => $data['khalti_env'] ?? 'test',
            'social_links'     => collect($data['social'] ?? [])->only(array_keys(ShopSetting::SOCIAL))->filter()->all(),
        ]);
        // The secret key is never shown again: empty keeps the saved one.
        if ($request->boolean('remove_khalti_key')) {
            $settings->khalti_secret_key = null;
        } elseif (filled($data['khalti_secret_key'] ?? null)) {
            $settings->khalti_secret_key = trim($data['khalti_secret_key']);
        }
        $settings->save();

        return redirect()->route('shop.admin.setting.index', ['tab' => $request->input('tab', 'store')])->with('success', 'Settings saved.');
    }
}
