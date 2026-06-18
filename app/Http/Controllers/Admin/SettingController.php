<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const DEFAULTS = [
        'theme_color'      => '#198754',
        'restaurant_name'  => '',
        'tagline'          => '',
        'currency'         => 'NPR',
        'tax_percent'      => '13',
        'hero_title'       => '',
        'hero_subtitle'    => '',
        'about_text'       => '',
        'contact_email'    => '',
        'contact_phone'    => '',
        'contact_address'  => '',
        'opening_hours'    => '',
        'facebook_url'     => '',
        'instagram_url'    => '',
        'twitter_url'      => '',
    ];

    public function index()
    {
        $company = Tenancy::company();

        $settings = collect(self::DEFAULTS)->map(
            fn ($default, $key) => $company ? Setting::getValue($company->id, $key, $default) : $default
        );

        return view('admin.theme_one.setting.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $company = Tenancy::company();
        abort_unless($company, 404);

        $data = $request->validate([
            'theme_color'      => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'restaurant_name'  => 'nullable|string|max:150',
            'tagline'          => 'nullable|string|max:255',
            'currency'         => 'nullable|string|max:10',
            'tax_percent'      => 'nullable|numeric|min:0|max:100',
            'hero_title'       => 'nullable|string|max:255',
            'hero_subtitle'    => 'nullable|string|max:500',
            'about_text'       => 'nullable|string|max:5000',
            'contact_email'    => 'nullable|email|max:255',
            'contact_phone'    => 'nullable|string|max:50',
            'contact_address'  => 'nullable|string|max:500',
            'opening_hours'    => 'nullable|string|max:500',
            'facebook_url'     => 'nullable|string|max:255',
            'instagram_url'    => 'nullable|string|max:255',
            'twitter_url'      => 'nullable|string|max:255',
        ]);

        foreach (self::DEFAULTS as $key => $default) {
            Setting::putValue($company->id, $key, $data[$key] ?? $default);
        }

        return back()->with('status', 'Settings saved successfully.');
    }
}
