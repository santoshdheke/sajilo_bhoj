<?php

$local = env('APP_ENV') === 'local';

return [
    'min_topup' => 100,
    'max_topup' => 100000,

    // Credit allowed below zero: the plan keeps running until the balance reaches -credit_limit (Nrs).
    'credit_limit' => 100,

    // Refer & Earn: when a client company (added by another company) tops up, the company
    // that added it gets this percentage of the amount on its own balance, automatically.
    'referral_bonus_percent' => 10,
    // Bonuses collect in a separate earnings wallet. The company moves them into its plan balance,
    // or withdraws them (at least this much per request, Nrs); the SajiloBhoj team pays withdrawals
    // out and marks them paid with: php artisan referrals:withdrawals
    'referral_min_withdrawal' => 100,
    // Optional: email the SajiloBhoj team when a withdrawal is requested.
    'payout_notify_email' => env('REFERRAL_PAYOUT_EMAIL'),

    /*
     * Payment gateways for balance top-ups. Balance is only added after the gateway confirms the payment.
     * Mode "test" uses the gateways' sandbox; "live" takes real money. A gateway without credentials is hidden.
     */
    'khalti' => [
        'mode'       => $khaltiMode = env('KHALTI_MODE', $local ? 'test' : 'live'),
        'secret_key' => env('KHALTI_SECRET_KEY'),   // test key: test-admin.khalti.com → Keys; live key: admin.khalti.com
        'base_url'   => $khaltiMode === 'live' ? 'https://khalti.com/api/v2/' : 'https://dev.khalti.com/api/v2/',
    ],

    'esewa' => [
        'mode'         => $esewaMode = env('ESEWA_MODE', $local ? 'test' : 'live'),
        // eSewa's public test merchant is used in test mode unless you set your own.
        'product_code' => env('ESEWA_PRODUCT_CODE', $esewaMode === 'test' ? 'EPAYTEST' : null),
        'secret_key'   => env('ESEWA_SECRET_KEY', $esewaMode === 'test' ? '8gBm/:&EnhH.1/q' : null),
        'form_url'     => $esewaMode === 'live' ? 'https://epay.esewa.com.np/api/epay/main/v2/form' : 'https://rc-epay.esewa.com.np/api/epay/main/v2/form',
        'status_url'   => $esewaMode === 'live' ? 'https://epay.esewa.com.np/api/epay/transaction/status/' : 'https://rc.esewa.com.np/api/epay/transaction/status/',
    ],
];
