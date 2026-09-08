<?php

return [
    'driver' => env('POSTCODE_DRIVER', 'postcodeeu'),

    'drivers' => [
        'postcodeeu' => [
            'key' => env('POSTCODE_EU_API_KEY'),
            'secret' => env('POSTCODE_EU_API_SECRET'),
        ],

        'pro6pp' => [
            'key' => env('PRO6PP_API_KEY'),
        ],

        'postcodeservice' => [
            // Test credentials are documented at https://developers.postcodeservice.com/#authenticating-requests
            'client_id' => env('POSTCODESERVICE_CLIENT_ID'),
            'secure_code' => env('POSTCODESERVICE_SECURE_CODE'),
        ],
    ],
];
