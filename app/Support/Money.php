<?php

namespace App\Support;

/**
 * Formats prices in the restaurant's currency (Settings → General → Currency).
 */
class Money
{
    public const SYMBOLS = ['NPR' => 'Rs.', 'USD' => '$', 'INR' => '₹', 'EUR' => '€', 'GBP' => '£'];

    public static function symbol(?string $code): string
    {
        $code = strtoupper((string) ($code ?: 'NPR'));

        return self::SYMBOLS[$code] ?? $code;
    }

    /** "Rs. 120", "Rs. 99.50", "$ 12.40" — whole amounts drop the decimals. */
    public static function format(float|int|string|null $amount, ?string $code): string
    {
        $amount = (float) $amount;
        $digits = floor($amount) == $amount ? 0 : 2;

        return self::symbol($code) . ' ' . number_format($amount, $digits);
    }
}
