<?php

namespace Modules\Shop\Support;

use Modules\Shop\Models\ShopSetting;

/**
 * The storefront theme of the current shop (Settings → Branding). Each theme is a folder in
 * resources/views/frontend (multiple_pasal's themes): "vegitable" (default) or "simple_theme".
 */
class Storefront
{
    public const THEMES = [
        'vegitable'    => 'Market (category sidebar, offers)',
        'simple_theme' => 'Simple (large banner, clean grid)',
    ];

    public const DEFAULT = 'vegitable';

    public static function theme(): string
    {
        $theme = ShopSetting::current()->theme;

        return isset(self::THEMES[$theme]) ? $theme : self::DEFAULT;
    }

    /** e.g. view('home.index') → "shop::frontend.vegitable.home.index". */
    public static function view(string $name): string
    {
        return 'shop::frontend.'.self::theme().'.'.$name;
    }
}
