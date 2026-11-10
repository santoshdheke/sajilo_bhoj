<?php

namespace App\Support\Payments;

use App\Models\TopupPayment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Khalti ePayment (v2): initiate -> person pays on Khalti -> back to return_url -> lookup to verify.
 * Amounts are sent in paisa. Docs: https://docs.khalti.com/khalti-epayment/
 */
class Khalti
{
    public static function configured(): bool
    {
        return (bool) config('billing.khalti.secret_key');
    }

    /** Create the payment on Khalti and return the URL to send the person to. */
    public static function initiate(TopupPayment $payment, string $returnUrl, string $websiteUrl, array $customer): string
    {
        $res = self::client()->post('epayment/initiate/', [
            'return_url'          => $returnUrl,
            'website_url'         => $websiteUrl,
            'amount'              => (int) round($payment->amount * 100),
            'purchase_order_id'   => $payment->reference,
            'purchase_order_name' => 'SajiloBhoj balance top-up',
            'customer_info'       => array_filter($customer),
        ]);

        if (! $res->successful() || ! $res->json('payment_url')) {
            $payment->update(['status' => 'failed', 'response' => $res->json() ?: ['http' => $res->status()]]);
            throw new RuntimeException(self::errorText($res->json()));
        }

        $payment->update(['gateway_token' => $res->json('pidx')]);

        return $res->json('payment_url');
    }

    /** Ask Khalti for the real status of a payment (never trust the return URL's query string alone). */
    public static function lookup(string $pidx): array
    {
        return self::client()->post('epayment/lookup/', ['pidx' => $pidx])->json() ?? [];
    }

    private static function client()
    {
        return Http::baseUrl(config('billing.khalti.base_url'))
            ->withHeaders(['Authorization' => 'Key ' . config('billing.khalti.secret_key')])
            ->acceptJson()->asJson()->timeout(20);
    }

    private static function errorText(?array $json): string
    {
        $first = collect($json ?? [])->except(['error_key'])->flatten()->first();

        return 'Khalti could not start the payment' . ($first ? ": {$first}" : '.') ;
    }
}
