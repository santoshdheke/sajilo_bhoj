<?php

namespace Modules\Shop\Support;

class Money
{
    /** "Rs. 1,250" (decimals only when there are paisa). */
    public static function format(float|int|string|null $amount): string
    {
        $amount = (float) $amount;
        $decimals = fmod($amount, 1.0) == 0.0 ? 0 : 2;

        return 'Rs. '.number_format($amount, $decimals);
    }
}
