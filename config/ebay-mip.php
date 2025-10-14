<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SFTP Connection Settings
    |--------------------------------------------------------------------------
    |
    | Configure SFTP connection to eBay MIP servers.
    | Get credentials from www.mip.ebay.com after account setup.
    |
    */
    'sftp' => [
        'host' => env('MIP_SFTP_HOST'),
        'username' => env('MIP_SFTP_USERNAME'),
        'password' => env('MIP_SFTP_PASSWORD'),
        'port' => env('MIP_SFTP_PORT', 22),
        'root' => env('MIP_SFTP_ROOT', '/'),
        'timeout' => env('MIP_SFTP_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic upload via Laravel Scheduler or manual mode.
    |
    */
    'upload' => [
        'enabled' => env('MIP_UPLOAD_ENABLED', false),
        'mode' => env('MIP_UPLOAD_MODE', 'sftp'), // sftp or manual
        'schedule' => env('MIP_UPLOAD_SCHEDULE', '0 2 * * *'), // Daily at 2 AM
        'retry_attempts' => env('MIP_UPLOAD_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('MIP_UPLOAD_RETRY_DELAY', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Feed Storage Settings
    |--------------------------------------------------------------------------
    |
    | Configure where feeds are stored and compression options.
    |
    */
    'feeds' => [
        'storage_path' => env('MIP_FEEDS_PATH', storage_path('mip/feeds')),
        'compression' => env('MIP_FEEDS_COMPRESSION', true), // zip feeds
        'keep_original' => env('MIP_FEEDS_KEEP_ORIGINAL', true), // keep unzipped
        'retention_days' => env('MIP_FEEDS_RETENTION_DAYS', 30), // auto-delete old feeds
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Settings
    |--------------------------------------------------------------------------
    |
    | Configure validation strictness and rules.
    |
    */
    'validation' => [
        'strict' => env('MIP_VALIDATION_STRICT', true),
        'throw_on_error' => env('MIP_VALIDATION_THROW', true),
    ],

];
