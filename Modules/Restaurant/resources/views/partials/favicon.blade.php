{{--
    Browser-tab icon: always the default SajiloBhoj serving-dish mark (never the uploaded
    company logo), drawn in the restaurant's theme colour. Pass ['faviconColor' => '#hex'] to override
    (a dedicated name, so a page's own $color variable can't leak in).
--}}
@php
    $fav = $faviconColor ?? $themeColor ?? ($settings['theme_color'] ?? null);
    $fav = is_string($fav) && preg_match('/^#[0-9a-f]{6}$/i', $fav) ? $fav : '#198754';
    // Same shades the logo uses: dark = 78% colour + black, soft = 30% colour + white.
    $mix = function (string $hex, int $with, float $keep) {
        return '#' . implode('', array_map(fn ($c) => sprintf('%02x', (int) round(hexdec($c) * $keep + $with * (1 - $keep))), str_split(ltrim($hex, '#'), 2)));
    };
    $favSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -45 240 240">'
        . '<g fill="none" stroke="' . $mix($fav, 255, .3) . '" stroke-width="7" stroke-linecap="round"><path d="M104 40c-9-11 9-15 0-27"/><path d="M122 36c-9-11 9-15 0-27"/></g>'
        . '<path d="M58 104a62 56 0 0 1 124 0z" fill="' . $fav . '"/>'
        . '<path d="M78 86a42 38 0 0 1 28-26" fill="none" stroke="#fff" stroke-width="7" stroke-linecap="round" opacity=".5"/>'
        . '<circle cx="120" cy="47" r="9" fill="' . $fav . '"/>'
        . '<rect x="50" y="100" width="140" height="11" rx="5.5" fill="' . $mix($fav, 0, .78) . '"/>'
        . '<path d="M176 88c10-20 30-26 50-24-8 20-28 30-50 24z" fill="' . $fav . '" opacity=".85"/>'
        . '<path d="M182 102c14-10 32-10 46 0-14 10-32 10-46 0z" fill="' . $mix($fav, 255, .3) . '"/>'
        . '<path d="M12 136c40-22 92-28 150-16 26 6 46 2 70-8-20 22-62 30-102 24-44-6-84-6-118 0z" fill="' . $fav . '"/>'
        . '</svg>';
@endphp
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,{{ rawurlencode($favSvg) }}">
