<?php

return [
    'stripe' => [
        'api_base' => env('STRIPE_API_BASE', 'https://api.stripe.com'),
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
    ],
];
