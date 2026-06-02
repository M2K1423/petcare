<?php

return [
    'enabled' => env('VNPAY_ENABLED', false),
    'tmn_code' => env('VNPAY_TMN_CODE'),
    'hash_secret' => env('VNPAY_HASH_SECRET'),
    'payment_url' => env('VNPAY_PAYMENT_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url' => env('VNPAY_RETURN_URL'),
    'ipn_url' => env('VNPAY_IPN_URL'),
    'locale' => env('VNPAY_LOCALE', 'vn'),
    'order_type' => env('VNPAY_ORDER_TYPE', 'other'),
    'expire_minutes' => (int) env('VNPAY_EXPIRE_MINUTES', 15),
];
