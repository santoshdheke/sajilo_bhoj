<?php

$local = env('APP_ENV') === 'local';

return [
    'min_topup' => 100,
    'max_topup' => 100000,

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
