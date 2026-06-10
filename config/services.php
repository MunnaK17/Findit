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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'claude' => [
    'api_key' => env('CLAUDE_API_KEY'),
    ],

    'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model'   => env('GEMINI_MODEL', 'gemini-2.0-flash'),
],

    'pusher' => [
 'app_id'       => env('PUSHER_APP_ID'),
    'app_key'      => env('PUSHER_APP_KEY'),
    'app_secret'   => env('PUSHER_APP_SECRET'),
    'cluster'      => env('PUSHER_CLUSTER', 'ap1'),
    'force_tls'    => true,
    ],

    'wa_fonnte' => [
    'enabled'  => env('WA_FONNTE_ENABLED', false),
    'api_key'  => env('WA_FONNTE_API_KEY'),
    'sender'   => env('WA_FONNTE_SENDER'),
    ],

    'wa' => [
    'enabled'      => env('WA_ENABLED', false),
    'provider'     => env('WA_PROVIDER', 'twilio'),
    'api_url'      => env('WA_API_URL'),
    'instance_id'   => env('WA_INSTANCE_ID'),
    'token'        => env('WA_TOKEN'),
    ],

    'twilio' => [
    'account_sid'      => env('TWILIO_ACCOUNT_SID'),
    'auth_token'       => env('TWILIO_AUTH_TOKEN'),
    'whatsapp_from'    => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
    ],

];
