<?php

return [
    'name' => 'Base',


    'sms' => [
        'default' => 'kavenegar',

        'connections' => [
            'kavenegar' => [
                'base_url' => 'https://api.kavenegar.com/v1/',
                'api_key' => env('KAVENEGAR_API_KEY'),
                'sender' => '10002000200404'
            ],

            'farazsms' => [
                'base_url' => 'https://api.kavenegar.com/v1/',
                'api_key' => env('KAVENEGAR_API_KEY'),
                'sender' => '10002000200404'
            ]
        ]

    ]
];
