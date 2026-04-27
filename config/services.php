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

    /*
    |--------------------------------------------------------------------------
    | OAuth / SSO Providers
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/google/callback',
    ],

    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/github/callback',
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/facebook/callback',
    ],

    'twitter' => [
        'client_id'     => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/twitter/callback',
    ],

    'linkedin-openid' => [
        'client_id'     => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/linkedin-openid/callback',
    ],

    'gitlab' => [
        'client_id'     => env('GITLAB_CLIENT_ID'),
        'client_secret' => env('GITLAB_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/gitlab/callback',
    ],

    'microsoft' => [
        'client_id'     => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/microsoft/callback',
    ],

    'apple' => [
        'client_id'     => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/apple/callback',
    ],

    'franceconnect' => [
        'client_id'     => env('FRANCECONNECT_CLIENT_ID'),
        'client_secret' => env('FRANCECONNECT_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/api/v1/auth/social/franceconnect/callback',
        // Sandbox: https://fcp.integ01.dev-franceconnect.fr
        // Production: https://app.franceconnect.gouv.fr
        'base_url'      => env('FRANCECONNECT_BASE_URL', 'https://fcp.integ01.dev-franceconnect.fr'),
    ],

];
