<?php

return [
    'currency' => 'UZS',

    'payme' => [
        'merchant_id' => env('PAYME_MERCHANT_ID', ''),
        'secret_key' => env('PAYME_SECRET_KEY', ''),
        'checkout_url' => env('PAYME_CHECKOUT_URL', 'https://checkout.paycom.uz'),
        'test_mode' => (bool) env('PAYME_TEST_MODE', true),
    ],

    'click' => [
        'service_id' => env('CLICK_SERVICE_ID', ''),
        'merchant_id' => env('CLICK_MERCHANT_ID', ''),
        'secret_key' => env('CLICK_SECRET_KEY', ''),
        'checkout_url' => env('CLICK_CHECKOUT_URL', 'https://my.click.uz/services/pay'),
    ],

    'return_route' => 'payments.return',
];
