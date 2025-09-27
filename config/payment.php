<?php

return [
    'momo' => [
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'partner_code' => env('MOMO_PARTNER_CODE', 'MOMO'),
        'access_key' => env('MOMO_ACCESS_KEY', ''),
        'secret_key' => env('MOMO_SECRET_KEY', ''),
        'redirect_url' => env('MOMO_REDIRECT_URL', env('APP_URL') . '/momo/callback'),
        'ipn_url' => env('MOMO_IPN_URL', env('APP_URL') . '/momo/ipn'),
    ],
];
