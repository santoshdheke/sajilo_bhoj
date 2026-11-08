<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Collection;

/**
 * A company's social media links, stored as a JSON list in the `social_links` setting:
 * [{"platform": "facebook", "url": "https://..."}, ...]
 *
 * Companies that saved the older single fields (facebook_url, instagram_url, ...)
 * are read from those until they save the new list.
 */
class SocialLinks
{
    /** platform key => [label, Bootstrap icon] */
    public const PLATFORMS = [
        'facebook'  => ['Facebook',    'bi-facebook'],
        'instagram' => ['Instagram',   'bi-instagram'],
        'tiktok'    => ['TikTok',      'bi-tiktok'],
        'youtube'   => ['YouTube',     'bi-youtube'],
        'x'         => ['X (Twitter)', 'bi-twitter-x'],
        'linkedin'  => ['LinkedIn',    'bi-linkedin'],
        'whatsapp'  => ['WhatsApp',    'bi-whatsapp'],
        'telegram'  => ['Telegram',    'bi-telegram'],
        'pinterest' => ['Pinterest',   'bi-pinterest'],
        'website'   => ['Website',     'bi-globe2'],
    ];

    private const LEGACY_KEYS = [
        'facebook'  => 'facebook_url',
        'instagram' => 'instagram_url',
        'x'         => 'twitter_url',
        'youtube'   => 'youtube_url',
    ];

    /**
     * @return Collection<int, array{platform: string, url: string, label: string, icon: string}>
     */
    public static function forCompany(?int $companyId): Collection
    {
        if (! $companyId) {
            return collect();
        }

        $items = json_decode((string) Setting::getValue($companyId, 'social_links', ''), true);

        if (! is_array($items)) {
            $items = [];
            foreach (self::LEGACY_KEYS as $platform => $key) {
                if ($url = Setting::getValue($companyId, $key)) {
                    $items[] = ['platform' => $platform, 'url' => $url];
                }
            }
        }

        return self::normalize($items);
    }

    /**
     * Keep only rows with a known platform and a URL, and add label + icon.
     */
    public static function normalize(array $items): Collection
    {
        return collect($items)
            ->filter(fn ($i) => is_array($i) && ! empty($i['url']) && isset(self::PLATFORMS[$i['platform'] ?? '']))
            ->map(fn ($i) => [
                'platform' => $i['platform'],
                'url'      => trim($i['url']),
                'label'    => self::PLATFORMS[$i['platform']][0],
                'icon'     => self::PLATFORMS[$i['platform']][1],
            ])
            ->values();
    }
}
