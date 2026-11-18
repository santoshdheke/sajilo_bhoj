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
        'facebook'  => ['label' => 'Facebook',  'icon' => 'fab fa-facebook-f', 'bi' => 'bi-facebook'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'fab fa-instagram', 'bi' => 'bi-instagram'],
        'tiktok'    => ['label' => 'TikTok',    'icon' => 'fab fa-tiktok', 'bi' => 'bi-tiktok'],
        'youtube'   => ['label' => 'YouTube',   'icon' => 'fab fa-youtube', 'bi' => 'bi-youtube'],
        'twitter'   => ['label' => 'X / Twitter', 'icon' => 'fab fa-twitter', 'bi' => 'bi-twitter-x'],
        'whatsapp'  => ['label' => 'WhatsApp',  'icon' => 'fab fa-whatsapp', 'bi' => 'bi-whatsapp'],
    ];

    /** Footer policy pages: settings column => [url slug, title, default text (":shop" = shop name)]. */
    public const POLICIES = [
        'privacy_policy' => ['privacy', 'Privacy Policy', ":shop collects only the details needed to process your orders, such as your name, phone number, email and delivery address. We use them to deliver your orders and contact you about them, and never sell them to anyone. Payments made through eSewa or Khalti are handled by those services. Contact us any time to update or remove your details."],
        'terms'          => ['terms', 'Terms & Conditions', "By ordering from :shop you agree to these terms. Prices and stock can change without notice; the price shown when you place an order is the price you pay. We may cancel an order if a product is unavailable, and refund anything already paid. Delivery times are estimates. Please check your order when it arrives."],
        'refund_policy'  => ['refund-policy', 'Refund Policy', "If a product from :shop arrives damaged, defective or different from what you ordered, contact us within 7 days of delivery. Keep the product unused and in its original packaging. After we check it we will replace it or refund your payment. Delivery charges are refunded only when the mistake was ours."],
    ];

    protected $connection = 'shop';

    protected $table = 'settings';

    protected $fillable = [
        'company_id', 'theme', 'logo_color', 'logo_color_two', 'text_color', 'font_family', 'announcement', 'about_us',
        'vat_number', 'default_tax_rate', 'receipt_footer', 'esewa_id', 'esewa_name', 'khalti_id', 'khalti_name',
        'khalti_secret_key', 'khalti_env', 'social_links', 'privacy_policy', 'terms', 'refund_policy',
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

    /** A policy page's text: the shop's own, or the default with the shop name filled in. */
    public function policy(string $key, string $shopName): string
    {
        return filled($this->$key) ? $this->$key : str_replace(':shop', $shopName, self::POLICIES[$key][2]);
    }

    /** Filled social links: platform => [url, label, icon]. */
    public function socialLinks(): array
    {
        return collect($this->social_links ?? [])->filter()
            ->filter(fn ($url, $platform) => isset(self::SOCIAL[$platform]))
            ->map(fn ($url, $platform) => ['url' => $url] + self::SOCIAL[$platform])->all();
    }
}
