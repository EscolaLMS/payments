<?php

return [
    /**
     * Defaults settings
     */
    'default_gateway' => env('PAYMENTS_DEFAULT_GATEWAY', 'Stripe'),
    'default_currency' => env('PAYMENTS_DEFAULT_CURRENCY', EscolaLms\Payments\Enums\Currency::USD),

    /** 
     * Driver specific settings 
     */
    'drivers' => [
        'free' => [],
        'stripe' => [
            'enabled' => true,
            'secret_key' => env('PAYMENTS_STRIPE_SECRET_KEY', 'sk_test_51Ig8icJ9tg9t712TG1Odn17fisxXM9y01YrDBxC4vd6FJMUsbB3bQvXYs8Oiz9U2GLH1mxwQ2BCjXcjc3gxEPKTT00tx6wtVco'),
            'publishable_key' => env('PAYMENTS_STRIPE_PUBLISHABLE_KEY', 'pk_test_51Ig8icJ9tg9t712TnCR6sKY9OXwWoFGWH4ERZXoxUVIemnZR0B6Ei0MzjjeuWgOzLYKjPNbT8NbG1ku1T2pGCP4B00GnY0uusI'),
            'allowed_payment_method_types' => ['card', 'p24'],
        ],
        'przelewy24' => [
            'enabled' => true,
            'live' => env('PAYMENTS_PRZELEWY24_LIVE', true),
            'merchant_id' => env('PAYMENTS_PRZELEWY24_MERCHANT_ID'),
            'pos_id' => env('PAYMENTS_PRZELEWY24_POS_ID'),
            'api_key' => env('PAYMENTS_PRZELEWY24_API_KEY'),
            'crc' => env('PAYMENTS_PRZELEWY24_CRC'),
        ],
    ]
];
