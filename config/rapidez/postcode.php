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
            // The defaults are the public test credentials.
            'client_id' => env('POSTCODESERVICE_CLIENT_ID', '1177'),
            'secure_code' => env('POSTCODESERVICE_SECURE_CODE', '9SRLYBCALURPE2B'),
        ],
    ],
];
