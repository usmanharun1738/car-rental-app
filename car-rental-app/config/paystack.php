<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paystack Keys
    |--------------------------------------------------------------------------
    |
    | Your Paystack publishable and secret keys.
    |
    */
    'public_key' => env('PAYSTACK_PUBLIC_KEY'),
    'secret_key' => env('PAYSTACK_SECRET_KEY'),
    'payment_url' => 'https://api.paystack.co',
    'merchant_email' => env('MERCHANT_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Callback URL
    |--------------------------------------------------------------------------
    |
    | The URL Paystack will redirect to after payment.
    |
    */
    'callback_url' => env('APP_URL') . '/payment/callback',
];
