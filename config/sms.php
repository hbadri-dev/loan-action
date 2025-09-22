<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for SMS services used in the
    | application. Currently configured for Kavenegar SMS service.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default SMS Service
    |--------------------------------------------------------------------------
    |
    | This option controls the default SMS service that will be used by the
    | application. You may change this to any service that implements the
    | SMS service interface.
    |
    */

    'default' => env('SMS_DRIVER', 'kavenegar'),

    /*
    |--------------------------------------------------------------------------
    | SMS Service Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure the SMS services for your application. You may
    | add multiple services and switch between them as needed.
    |
    */

    'services' => [
        'kavenegar' => [
            'api_key' => env('KAVENEGAR_API_KEY'),
            'base_url' => 'https://api.kavenegar.com/v1',
            'sender' => env('KAVENEGAR_SENDER', '10008663'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | When sandbox mode is enabled, SMS messages will be logged instead of
    | actually being sent. This is useful for development and testing.
    |
    */

    'sandbox' => env('SMS_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for SMS sending to prevent abuse and control
    | costs. Values are in seconds.
    |
    */

    'rate_limit' => [
        'max_attempts' => env('SMS_RATE_LIMIT_ATTEMPTS', 5),
        'decay_minutes' => env('SMS_RATE_LIMIT_DECAY', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Templates
    |--------------------------------------------------------------------------
    |
    | Define the SMS templates used by the application. These templates
    | should be configured in your SMS service provider dashboard.
    |
    */

    'templates' => [
        'login_otp' => 'login-otp',
        'contract_confirmation' => 'contract-confirmation',
        'payment_approved' => 'payment-approved',
        'payment_rejected' => 'payment-rejected',
        'bid_accepted' => 'bid-accepted',
        'sale_completed' => 'sale-completed',
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    |
    | General settings for SMS functionality.
    |
    */

    'settings' => [
        'otp_length' => env('SMS_OTP_LENGTH', 6),
        'otp_expiry_minutes' => env('SMS_OTP_EXPIRY', 2),
        'max_retries' => env('SMS_MAX_RETRIES', 3),
        'timeout' => env('SMS_TIMEOUT', 30),
        'connect_timeout' => env('SMS_CONNECT_TIMEOUT', 10),
    ],
];

