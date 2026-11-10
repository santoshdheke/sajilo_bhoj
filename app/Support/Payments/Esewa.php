<?php

namespace App\Support\Payments;

use App\Models\TopupPayment;
use Illuminate\Support\Facades\Http;

/**
 * eSewa ePay (v2): signed form POST to eSewa -> person pays -> success_url?data=<base64 JSON>
 * -> check the signature and the transaction status API before adding balance.
 * Docs: https://developer.esewa.com.np/pages/Epay
 */
class Esewa
{
    public static function configured(): bool
    {
        return config('billing.esewa.product_code') && config('billing.esewa.secret_key');
    }

    /** Fields for the auto-submitting form that sends the person to eSewa. */
    public static function formFields(TopupPayment $payment, string $successUrl, string $failureUrl): array
    {
        $fields = [
            'amount'                  => self::money($payment->amount),
            'tax_amount'              => '0',
            'total_amount'            => self::money($payment->amount),
            'transaction_uuid'        => $payment->reference,
            'product_code'            => config('billing.esewa.product_code'),
            'product_service_charge'  => '0',
            'product_delivery_charge' => '0',
            'success_url'             => $successUrl,
            'failure_url'             => $failureUrl,
            'signed_field_names'      => 'total_amount,transaction_uuid,product_code',
        ];
        $fields['signature'] = self::sign($fields, $fields['signed_field_names']);

        return $fields;
    }

    /** Decode and check the signature of the data eSewa sends back. Returns null when it's not genuine. */
    public static function decode(?string $data): ?array
    {
        $json = json_decode(base64_decode((string) $data, true) ?: '', true);
        if (! is_array($json) || empty($json['signed_field_names']) || empty($json['signature'])) {
            return null;
        }

        return hash_equals(self::sign($json, $json['signed_field_names']), (string) $json['signature']) ? $json : null;
    }

    /** eSewa's own record of the transaction (status COMPLETE when paid). */
    public static function status(TopupPayment $payment): array
    {
        return Http::timeout(20)->acceptJson()->get(config('billing.esewa.status_url'), [
            'product_code'     => config('billing.esewa.product_code'),
            'total_amount'     => self::money($payment->amount),
            'transaction_uuid' => $payment->reference,
        ])->json() ?? [];
    }

    private static function sign(array $fields, string $names): string
    {
        $message = collect(explode(',', $names))->map(fn ($n) => $n . '=' . ($fields[$n] ?? ''))->implode(',');

        return base64_encode(hash_hmac('sha256', $message, config('billing.esewa.secret_key'), true));
    }

    /** eSewa compares amounts as text: send whole rupees without decimals, otherwise 2 decimals. */
    public static function money($amount): string
    {
        $amount = round((float) $amount, 2);

        return fmod($amount, 1.0) == 0.0 ? (string) (int) $amount : number_format($amount, 2, '.', '');
    }
}
