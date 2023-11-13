<?php

return [
    'totp' => [
        'secret_length' => 20,
        'default' => [
            'digits'    => 6,
            'seconds'   => 30,
            'algorithm' => 'sha1',
        ],
        'recovery' => [
            'enabled' => true,
            'codes'   => 10,
            'length'  => 8,
        ],
    ],
    'otp' => [
        'driver' => env('OTP_DRIVER', 'cache'),
        'token_lifetime'   => env('OTP_TOKEN_LIFE_TIME', 5),
        'token_length'     => env('OTP_TOKEN_LENGTH', 5),
        'prefix'           => 'otp_',
        'channel'           => '',
    ]
];