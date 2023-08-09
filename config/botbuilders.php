<?php

return [
    'everwebinar' =>  [
        'base_url' => env('EVERWEBINAR_BASEURL', 'https://api.webinarjam.com'),
        'url' => env('EVERWEBINAR_URL', 'https://api.webinarjam.com/everwebinar'),
        'api_key' => env('EVERWEBINAR_API_KEY'),
        'webinar_id' => env('EVERWEBINAR_ID', '111')
    ],

    'ipapi' => [
        'base_url' => env('IPAPI_BASEURL', 'https://pro.ip-api.com'),
        'url' => env('IPAPI_URL', 'https://pro.ip-api.com/json'),
        'api_key' => env('IPAPI_API_KEY')
    ],

    'kartra' => [
        'url' => env('KARTRA_URL', 'https://app.kartra.com/api'),
        'id' => env('KARTRA_ID'),
        'key' => env('KARTRA_KEY'),
        'password' => env('KARTRA_PASSWORD')
    ],

    'zapier' => [
        'url' => env('ZAPIER_URL')
    ]
];
