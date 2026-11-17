<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Shop\Models\Concerns\BelongsToShop;
use Modules\Shop\Support\ShopTenancy;

/**
 * Storefront settings of a shop, one row per shop (multiple_pasal kept these on its companies table):
 * branding colours (hex without "#"), font, texts, QR / Khalti payment details and social links.
 */
class ShopSetting extends Model
{
    use BelongsToShop;

    public const FONTS = ['Inter', 'Poppins', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Nunito', 'Raleway', 'Mukta', 'Playfair Display'];

    public const SOCIAL = [
        'facebook'  => ['label' => 'Facebook',  'icon' => 'fab fa-facebook-f'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'fab fa-instagram'],
        'tiktok'    => ['label' => 'TikTok',    'icon' => 'fab fa-tiktok'],
        'youtube'   => ['label' => 'YouTube',   'icon' => 'fab fa-youtube'],
        'twitter'   => ['label' => 'X / Twitter', 'icon' => 'fab fa-twitter'],
        'whatsapp'  => ['label' => 'WhatsApp',  'icon' => 'fab fa-whatsapp'],
    ];

    protected $connection = 'shop';

    protected $table = 'settings';

    protected $fillable = [
        'company_id', 'logo_color', 'logo_color_two', 'text_color', 'font_family', 'announcement', 'about_us',
        'vat_number', 'default_tax_rate', 'receipt_footer', 'esewa_id', 'esewa_name', 'khalti_id', 'khalti_name',
        'khalti_secret_key', 'khalti_env', 'social_links',
    ];

    protected $hidden = ['khalti_secret_key'];

    protected function casts(): array
    {
        return [
            'khalti_secret_key' => 'encrypted',
            'social_links'      => 'array',
            'default_tax_rate'  => 'decimal:2',
        ];
    }

    /** Settings of the current shop (created with defaults the first time). */
    public static function current(): self
    {
        $companyId = ShopTenancy::id();

        return static::firstOrCreate(['company_id' => $companyId]);
    }

    /** Colours (hex without "#") and the Google Fonts name used by the storefront and back office. */
    public function brand(): array
    {
        return [
            'primary'   => $this->logo_color ?: 'f97316',
            'secondary' => $this->logo_color_two ?: '1c252e',
            'text'      => $this->text_color ?: 'ffffff',
            'font'      => str_replace(' ', '+', $this->font_family ?: 'Inter'),
            'font_css'  => $this->font_family ?: 'Inter',
        ];
    }

    /** Filled social links: platform => [url, label, icon]. */
    public function socialLinks(): array
    {
        return collect($this->social_links ?? [])->filter()
            ->filter(fn ($url, $platform) => isset(self::SOCIAL[$platform]))
            ->map(fn ($url, $platform) => ['url' => $url] + self::SOCIAL[$platform])->all();
    }
}
