<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'kavenegar' => [
        'api_key' => env('KAVENEGAR_API_KEY'),
        'sender' => env('KAVENEGAR_SENDER', '10008663'),
        'base_url' => env('KAVENEGAR_BASE_URL', 'https://api.kavenegar.com/v1'),
    ],

    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID', '3163ddfe-bd9a-46d2-830e-d2587c67ee46'),
        'sandbox' => filter_var(env('ZARINPAL_SANDBOX', true), FILTER_VALIDATE_BOOLEAN), // true برای sandbox، false برای production
        'callback_url' => env('ZARINPAL_CALLBACK_URL', 'http://localhost:8080/payment/callback'),
        'test_merchant_id' => '00000000-0000-0000-0000-000000000000', // Merchant ID تست برای sandbox
    ],

];
