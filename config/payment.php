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
    'vnpay' => [
        'endpoint' => env('VNPAY_ENDPOINT', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'tmn_code' => env('VNPAY_TMNCODE', 'N3AB31WQ'),
        'version' => env('VNPAY_VERSION', '2.1.1'),
        'return_url' => env('VNPAY_RETURN_URL', env('APP_URL') . '/vnpay/return'),
        'secret_key' => env('VNPAY_SECRET_KEY', 'FCIUBKDHMTQDPMYWJNBOIFFMACMRSTZC'),
    ],
];
