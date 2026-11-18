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
use Modules\Shop\Support\Storefront;

/**
 * Website settings, one page per section (laid out like the restaurant's website settings):
 * general store details and logo, theme (colours, font, design), payments, contact & social
 * links and policies.
 */
class SettingController extends Controller
{
    /** Settings pages shown in the settings sidebar: key => [label, icon]. */
    public const SECTIONS = [
        'general'  => ['label' => 'General',          'icon' => 'bi-info-circle'],
        'theme'    => ['label' => 'Theme',            'icon' => 'bi-palette'],
        'payment'  => ['label' => 'Payments',         'icon' => 'bi-wallet2'],
        'contact'  => ['label' => 'Contact & Social', 'icon' => 'bi-envelope'],
        'policies' => ['label' => 'Policies',         'icon' => 'bi-file-earmark-text'],
    ];

    /** Default colours (hex without "#"), used by "Reset to default". */
    public const DEFAULT_COLORS = ['logo_color' => 'f97316', 'logo_color_two' => '1c252e', 'text_color' => 'ffffff'];

    /** Old ?tab= links (before the settings were split into pages). */
    private const OLD_TABS = ['store' => 'general', 'branding' => 'theme', 'social' => 'contact'];

    public function index(Request $request, string $section = 'general'): View|RedirectResponse
    {
        if ($request->filled('tab')) {
            $tab = $request->query('tab');

            return redirect()->route('shop.admin.setting.index', self::OLD_TABS[$tab] ?? (isset(self::SECTIONS[$tab]) ? $tab : 'general'));
        }

        return view('shop::admin.theme_one.setting.index', [
            'section'  => $section,
            'sections' => self::SECTIONS,
            'company'  => ShopTenancy::company(),
            'settings' => ShopSetting::current(),
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        $settings = ShopSetting::current();
        $company  = ShopTenancy::company();

        match ($section) {
            'general'  => $this->saveGeneral($request, $company, $settings),
            'theme'    => $this->saveTheme($request, $settings),
            'payment'  => $this->savePayment($request, $settings),
            'contact'  => $this->saveContact($request, $company, $settings),
            'policies' => $this->savePolicies($request, $settings),
        };

        $message = $section === 'theme' && $request->boolean('reset_theme') ? 'Colours reset to the default.' : 'Settings saved.';

        return redirect()->route('shop.admin.setting.index', $section)->with('success', $message);
    }

    private function saveGeneral(Request $request, $company, ShopSetting $settings): void
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'remove_logo'      => 'nullable|boolean',
            'about_us'         => 'nullable|string|max:2000',
            'vat_number'       => 'nullable|string|max:50',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'receipt_footer'   => 'nullable|string|max:500',
        ]);

        // Store name and logo live on the company (common database).
        $company->name = $data['name'];
        if ($request->hasFile('logo') || $request->boolean('remove_logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->hasFile('logo') ? $request->file('logo')->store('shop/'.$company->id.'/logo', 'public') : null;
        }
        $company->save();

        $settings->fill([
            'about_us'         => $data['about_us'] ?? null,
            'vat_number'       => $data['vat_number'] ?? null,
            'default_tax_rate' => $data['default_tax_rate'] ?? 0,
            'receipt_footer'   => $data['receipt_footer'] ?? null,
        ])->save();
    }

    private function saveTheme(Request $request, ShopSetting $settings): void
    {
        if ($request->boolean('reset_theme')) {
            $settings->fill(self::DEFAULT_COLORS)->save();

            return;
        }

        $hex  = ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $data = $request->validate([
            'logo_color'     => ['required', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'logo_color_two' => $hex,
            'text_color'     => $hex,
            'font_family'    => ['nullable', Rule::in(ShopSetting::FONTS)],
            'theme'          => ['nullable', Rule::in(array_keys(Storefront::THEMES))],
            'announcement'   => 'nullable|string|max:255',
        ], [], ['logo_color' => 'brand colour']);

        // Colours are kept without "#", as the storefront expects.
        $color = fn (?string $v, string $current) => strtolower(ltrim($v ?: $current, '#'));

        $settings->fill([
            'logo_color'     => $color($data['logo_color'], $settings->logo_color),
            'logo_color_two' => $color($data['logo_color_two'] ?? null, $settings->logo_color_two),
            'text_color'     => $color($data['text_color'] ?? null, $settings->text_color),
            'font_family'    => $data['font_family'] ?? $settings->font_family,
            'theme'          => $data['theme'] ?? $settings->theme,
            'announcement'   => $data['announcement'] ?? null,
        ])->save();
    }

    private function savePayment(Request $request, ShopSetting $settings): void
    {
        $data = $request->validate([
            'esewa_id'          => 'nullable|string|max:100',
            'esewa_name'        => 'nullable|string|max:100',
            'khalti_id'         => 'nullable|string|max:100',
            'khalti_name'       => 'nullable|string|max:100',
            'khalti_secret_key' => 'nullable|string|max:255',
            'remove_khalti_key' => 'nullable|boolean',
            'khalti_env'        => 'nullable|in:test,live',
        ]);

        $settings->fill([
            'esewa_id'    => $data['esewa_id'] ?? null,
            'esewa_name'  => $data['esewa_name'] ?? null,
            'khalti_id'   => $data['khalti_id'] ?? null,
            'khalti_name' => $data['khalti_name'] ?? null,
            'khalti_env'  => $data['khalti_env'] ?? 'test',
        ]);
        // The secret key is never shown again: empty keeps the saved one.
        if ($request->boolean('remove_khalti_key')) {
            $settings->khalti_secret_key = null;
        } elseif (filled($data['khalti_secret_key'] ?? null)) {
            $settings->khalti_secret_key = trim($data['khalti_secret_key']);
        }
        $settings->save();
    }

    private function saveContact(Request $request, $company, ShopSetting $settings): void
    {
        $data = $request->validate([
            'email'    => 'nullable|email|max:255',
            'phone'    => 'nullable|string|max:30',
            'address'  => 'nullable|string|max:255',
            'social'   => 'nullable|array',
            'social.*' => 'nullable|url|max:255',
        ]);

        $company->fill([
            'email'   => $data['email'] ?? null,
            'phone'   => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ])->save();

        $settings->social_links = collect($data['social'] ?? [])->only(array_keys(ShopSetting::SOCIAL))->filter()->all();
        $settings->save();
    }

    private function savePolicies(Request $request, ShopSetting $settings): void
    {
        $data = $request->validate(collect(ShopSetting::POLICIES)->map(fn () => 'nullable|string|max:20000')->all());

        $settings->fill(collect(ShopSetting::POLICIES)->map(fn ($p, $field) => $data[$field] ?? null)->all())->save();
    }
}
