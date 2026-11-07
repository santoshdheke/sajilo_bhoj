<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public const SECTIONS = [
        'general' => [
            'label'        => 'General',
            'icon'         => 'bi-info-circle',
            'fields'       => [
                'restaurant_name' => '',
                'tagline'         => '',
                'currency'        => 'NPR',
                'tax_percent'     => '13',
            ],
        ],
        'theme' => [
            'label'  => 'Theme',
            'icon'   => 'bi-palette',
            'fields' => [
                'theme_color' => '#198754',
            ],
        ],
        'homepage' => [
            'label'  => 'Homepage',
            'icon'   => 'bi-house-door',
            'fields' => [
                'hero_title'              => '',
                'hero_subtitle'           => '',
                'home_why_title'          => '',
                'home_why_subtitle'       => '',
                'home_feature_1_icon'     => 'bi-cup-hot',
                'home_feature_1_title'    => 'Fresh Ingredients',
                'home_feature_1_desc'     => 'We prepare every dish using carefully selected vegetables, spices, and locally-sourced ingredients.',
                'home_feature_2_icon'     => 'bi-alarm',
                'home_feature_2_title'    => 'Quick Service',
                'home_feature_2_desc'     => 'Fast order handling and a smooth dine-in experience. We respect your time as much as your appetite.',
                'home_feature_3_icon'     => 'bi-emoji-smile',
                'home_feature_3_title'    => 'Cozy Atmosphere',
                'home_feature_3_desc'     => 'A warm interior, soft lighting, and elegant presentation make every meal an occasion worth revisiting.',
                'home_feature_4_icon'     => 'bi-calendar-check',
                'home_feature_4_title'    => 'Easy Reservation',
                'home_feature_4_desc'     => 'Book your table online in seconds — choose your room, date, and party size without any hassle.',
                'home_featured_badge'     => 'Popular Picks',
                'home_featured_title'     => 'Best sellers from our kitchen',
                'home_featured_text'      => "From spicy starters to refreshing drinks and indulgent desserts — there's something for everyone.",
                'home_testimonial_badge'  => 'Guest Review',
                'home_testimonial_quote'  => 'Fresh food, warm service — guests never leave disappointed!',
                'home_testimonial_author' => '— Happy Guest ⭐⭐⭐⭐⭐',
                'opening_hours'           => '',
            ],
        ],
        'about' => [
            'label'  => 'About',
            'icon'   => 'bi-file-text',
            'fields' => [
                'about_text' => '',
            ],
        ],
        'contact' => [
            'label'  => 'Contact',
            'icon'   => 'bi-envelope',
            'fields' => [
                'contact_email'   => '',
                'contact_phone'   => '',
                'contact_address' => '',
            ],
        ],
        'social' => [
            'label'  => 'Social Links',
            'icon'   => 'bi-share',
            'fields' => [
                'facebook_url'  => '',
                'instagram_url' => '',
                'twitter_url'   => '',
                'youtube_url'   => '',
            ],
        ],
    ];

    public static function allDefaults(): array
    {
        return collect(self::SECTIONS)->flatMap(fn ($section) => $section['fields'])->all();
    }

    public function index(string $section = 'general')
    {
        $company = Tenancy::company();
        $fields  = self::SECTIONS[$section]['fields'];

        $settings = collect($fields)->map(
            fn ($default, $key) => $company ? Setting::getValue($company->id, $key, $default) : $default
        );

        return view('admin.theme_one.setting.index', [
            'section'  => $section,
            'sections' => self::SECTIONS,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        $company = Tenancy::company();
        abort_unless($company, 404);

        $fields = self::SECTIONS[$section]['fields'];
        $rules  = $this->rulesFor($section);

        $data = $request->validate($rules);

        foreach ($fields as $key => $default) {
            Setting::putValue($company->id, $key, $data[$key] ?? $default);
        }

        if ($section === 'general') {
            $this->saveLogo($request, $company);
        }

        return redirect()->route('admin.settings.index', $section)->with('status', 'Settings saved successfully.');
    }

    /**
     * Store, replace or remove the company logo (companies.logo, public disk).
     */
    private function saveLogo(Request $request, $company): void
    {
        $old = $company->logo;

        if ($request->hasFile('logo')) {
            $company->update(['logo' => $request->file('logo')->store("logos/{$company->id}", 'public')]);
        } elseif ($request->boolean('remove_logo')) {
            $company->update(['logo' => null]);
        } else {
            return;
        }

        if ($old && $old !== $company->logo) {
            Storage::disk('public')->delete($old);
        }
    }

    private function rulesFor(string $section): array
    {
        return match ($section) {
            'general' => [
                'restaurant_name' => 'nullable|string|max:150',
                'tagline'         => 'nullable|string|max:255',
                'currency'        => 'nullable|string|max:10',
                'tax_percent'     => 'nullable|numeric|min:0|max:100',
                'logo'            => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
                'remove_logo'     => 'nullable|boolean',
            ],
            'theme' => [
                'theme_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            ],
            'homepage' => [
                'hero_title'              => 'nullable|string|max:255',
                'hero_subtitle'            => 'nullable|string|max:500',
                'home_why_title'          => 'nullable|string|max:255',
                'home_why_subtitle'       => 'nullable|string|max:500',
                'home_feature_1_icon'     => 'nullable|string|max:60',
                'home_feature_1_title'    => 'nullable|string|max:100',
                'home_feature_1_desc'     => 'nullable|string|max:500',
                'home_feature_2_icon'     => 'nullable|string|max:60',
                'home_feature_2_title'    => 'nullable|string|max:100',
                'home_feature_2_desc'     => 'nullable|string|max:500',
                'home_feature_3_icon'     => 'nullable|string|max:60',
                'home_feature_3_title'    => 'nullable|string|max:100',
                'home_feature_3_desc'     => 'nullable|string|max:500',
                'home_feature_4_icon'     => 'nullable|string|max:60',
                'home_feature_4_title'    => 'nullable|string|max:100',
                'home_feature_4_desc'     => 'nullable|string|max:500',
                'home_featured_badge'     => 'nullable|string|max:100',
                'home_featured_title'     => 'nullable|string|max:255',
                'home_featured_text'      => 'nullable|string|max:500',
                'home_testimonial_badge'  => 'nullable|string|max:100',
                'home_testimonial_quote'  => 'nullable|string|max:500',
                'home_testimonial_author' => 'nullable|string|max:150',
                'opening_hours'           => 'nullable|string|max:500',
            ],
            'about' => [
                'about_text' => 'nullable|string|max:5000',
            ],
            'contact' => [
                'contact_email'   => 'nullable|email|max:255',
                'contact_phone'   => 'nullable|string|max:50',
                'contact_address' => 'nullable|string|max:500',
            ],
            'social' => [
                'facebook_url'  => 'nullable|string|max:255',
                'instagram_url' => 'nullable|string|max:255',
                'twitter_url'   => 'nullable|string|max:255',
                'youtube_url'   => 'nullable|string|max:255',
            ],
            default => [],
        };
    }
}
