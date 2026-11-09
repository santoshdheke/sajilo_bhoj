<?php

namespace App\Support;

/**
 * Small PNG icons for emails, drawn in the restaurant's brand colour.
 * Email apps don't show SVG or icon fonts reliably, so icons are drawn with GD:
 * at 4× size, then scaled down for smooth edges (output is 2× for sharp screens).
 */
class EmailIcons
{
    private const S = 4; // supersampling factor

    /**
     * The website's default logo mark (partials/brand-logo: dish, steam, leaves, swoosh),
     * same shapes and shades: main = brand, dark = 78% brand + black, soft = 30% brand + white.
     * Displayed at 96×60 (SVG viewBox 240×150 scaled by 0.4).
     */
    public static function brandMark(string $hex): string
    {
        return self::draw(96, 60, function ($im, $s) use ($hex) {
            $k = 0.4 * $s; // viewBox unit -> pixels
            $main = self::rgb($im, $hex);
            $dark = self::rgb($im, self::mix($hex, .78, false));
            $soft = self::rgb($im, self::mix($hex, .30));
            [$r, $g, $b] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');
            $leaf = imagecolorallocatealpha($im, $r, $g, $b, 19); // ~85% opacity
            $shine = imagecolorallocatealpha($im, 255, 255, 255, 64); // ~50% white
            $pt = fn (array $p) => array_merge(...array_map(fn ($xy) => [(int) round($xy[0] * $k), (int) round($xy[1] * $k)], $p));

            // steam (stroked S-curves)
            foreach ([[[104, 40], [95, 29], [113, 25], [104, 13]], [[122, 36], [113, 25], [131, 21], [122, 9]]] as $c) {
                $pts = self::cubic(...$c);
                for ($i = 1; $i < count($pts); $i++) {
                    self::line($im, $pts[$i - 1][0] * $k, $pts[$i - 1][1] * $k, $pts[$i][0] * $k, $pts[$i][1] * $k, 7 * $k, $soft);
                }
            }
            // dome, knob, plate
            imagefilledarc($im, (int) (120 * $k), (int) (104 * $k), (int) (124 * $k), (int) (112 * $k), 180, 360, $main, IMG_ARC_PIE);
            imagefilledellipse($im, (int) (120 * $k), (int) (47 * $k), (int) (18 * $k), (int) (18 * $k), $main);
            self::line($im, 55.5 * $k, 105.5 * $k, 184.5 * $k, 105.5 * $k, 11 * $k, $dark);
            // shine on the dome
            $sh = self::quad([78, 86], [84, 64], [106, 60]);
            for ($i = 1; $i < count($sh); $i++) {
                self::line($im, $sh[$i - 1][0] * $k, $sh[$i - 1][1] * $k, $sh[$i][0] * $k, $sh[$i][1] * $k, 7 * $k, $shine);
            }
            // leaves
            imagefilledpolygon($im, $pt([...self::cubic([176, 88], [186, 68], [206, 62], [226, 64]), ...self::cubic([226, 64], [218, 84], [198, 94], [176, 88])]), $leaf);
            imagefilledpolygon($im, $pt([...self::cubic([182, 102], [196, 92], [214, 92], [228, 102]), ...self::cubic([228, 102], [214, 112], [196, 112], [182, 102])]), $soft);
            // swoosh underneath
            imagefilledpolygon($im, $pt([
                ...self::cubic([12, 136], [52, 114], [104, 108], [162, 120]),
                ...self::cubic([162, 120], [188, 126], [208, 122], [232, 112]),
                ...self::cubic([232, 112], [212, 134], [170, 142], [130, 136]),
                ...self::cubic([130, 136], [86, 130], [46, 130], [12, 136]),
            ]), $main);
        });
    }

    /** Points along a cubic Bézier curve. */
    private static function cubic(array $p0, array $p1, array $p2, array $p3, int $n = 24): array
    {
        $out = [];
        for ($i = 0; $i <= $n; $i++) {
            $t = $i / $n; $u = 1 - $t;
            $out[] = [
                $u ** 3 * $p0[0] + 3 * $u ** 2 * $t * $p1[0] + 3 * $u * $t ** 2 * $p2[0] + $t ** 3 * $p3[0],
                $u ** 3 * $p0[1] + 3 * $u ** 2 * $t * $p1[1] + 3 * $u * $t ** 2 * $p2[1] + $t ** 3 * $p3[1],
            ];
        }

        return $out;
    }

    /** Points along a quadratic Bézier curve. */
    private static function quad(array $p0, array $p1, array $p2, int $n = 16): array
    {
        $out = [];
        for ($i = 0; $i <= $n; $i++) {
            $t = $i / $n; $u = 1 - $t;
            $out[] = [$u * $u * $p0[0] + 2 * $u * $t * $p1[0] + $t * $t * $p2[0], $u * $u * $p0[1] + 2 * $u * $t * $p1[1] + $t * $t * $p2[1]];
        }

        return $out;
    }

    /** Filled shield with a white check. Displayed at 34×38. */
    public static function shield(string $hex): string
    {
        return self::draw(34, 38, function ($im, $s) use ($hex) {
            $c = self::rgb($im, $hex);
            $pts = [17, 1, 32, 6, 31, 19, 26, 29, 17, 37, 8, 29, 3, 19, 2, 6];
            imagefilledpolygon($im, array_map(fn ($v) => (int) ($v * $s), $pts), $c);
            $w = imagecolorallocate($im, 255, 255, 255);
            self::line($im, 10 * $s, 19 * $s, 15 * $s, 24 * $s, 3.4 * $s, $w);
            self::line($im, 15 * $s, 24 * $s, 24 * $s, 13 * $s, 3.4 * $s, $w);
        });
    }

    /** Outlined clock. Displayed at 24×24. */
    public static function clock(string $hex): string
    {
        return self::draw(24, 24, function ($im, $s) use ($hex) {
            $c = self::rgb($im, $hex);
            imagesetthickness($im, 1);
            for ($t = 0; $t < 2.2 * $s; $t++) { // thick ring
                imageellipse($im, 12 * $s, 12 * $s, (int) (21 * $s - $t * 2), (int) (21 * $s - $t * 2), $c);
            }
            self::line($im, 12 * $s, 12 * $s, 12 * $s, 6.5 * $s, 2.2 * $s, $c);
            self::line($im, 12 * $s, 12 * $s, 16 * $s, 14.5 * $s, 2.2 * $s, $c);
        });
    }

    /** Filled heart. Displayed at 22×20. */
    public static function heart(string $hex): string
    {
        return self::draw(22, 20, function ($im, $s) use ($hex) {
            $c = self::rgb($im, $hex);
            imagefilledellipse($im, (int) (6.2 * $s), (int) (6.5 * $s), (int) (11.6 * $s), (int) (11.6 * $s), $c);
            imagefilledellipse($im, (int) (15.8 * $s), (int) (6.5 * $s), (int) (11.6 * $s), (int) (11.6 * $s), $c);
            imagefilledpolygon($im, array_map(fn ($v) => (int) ($v * $s), [1, 8.5, 21, 8.5, 11, 19]), $c);
        });
    }

    private static function draw(int $w, int $h, callable $paint): string
    {
        $s = self::S * 2; // draw at 8× of display = 4× of the 2× output
        $big = imagecreatetruecolor($w * $s, $h * $s);
        imagesavealpha($big, true);
        imagealphablending($big, false);
        imagefill($big, 0, 0, imagecolorallocatealpha($big, 0, 0, 0, 127));
        imagealphablending($big, true);
        $paint($big, $s);

        $out = imagecreatetruecolor($w * 2, $h * 2);
        imagesavealpha($out, true);
        imagealphablending($out, false);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        imagecopyresampled($out, $big, 0, 0, 0, 0, $w * 2, $h * 2, $w * $s, $h * $s);

        ob_start();
        imagepng($out);
        imagedestroy($big);
        imagedestroy($out);

        return ob_get_clean();
    }

    /** Thick line with round caps. */
    private static function line($im, float $x1, float $y1, float $x2, float $y2, float $width, int $color): void
    {
        imagesetthickness($im, (int) round($width));
        imageline($im, (int) $x1, (int) $y1, (int) $x2, (int) $y2, $color);
        imagesetthickness($im, 1);
        $r = (int) round($width);
        imagefilledellipse($im, (int) $x1, (int) $y1, $r, $r, $color);
        imagefilledellipse($im, (int) $x2, (int) $y2, $r, $r, $color);
    }

    private static function rgb($im, string $hex): int
    {
        [$r, $g, $b] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');

        return imagecolorallocate($im, $r, $g, $b);
    }

    /** Mix a colour with white (amount 0–1 of the colour kept) or black (toWhite=false). */
    public static function mix(string $hex, float $keep, bool $toWhite = true): string
    {
        $with = $toWhite ? 255 : 0;

        return '#' . implode('', array_map(
            fn ($c) => sprintf('%02x', (int) round(hexdec($c) * $keep + $with * (1 - $keep))),
            str_split(ltrim($hex, '#'), 2),
        ));
    }
}
