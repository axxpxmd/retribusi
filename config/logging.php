<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack", "skrd"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'skrd_create' => [
            'driver' => 'daily',
            'path' => storage_path('logs/skrd/create.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'skrd_edit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/skrd/edit.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'skrd_delete' => [
            'driver' => 'daily',
            'path' => storage_path('logs/skrd/delete.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'create_qris' => [
            'driver' => 'daily',
            'path' => storage_path('logs/qris/create_qris.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'token' => [
            'driver' => 'daily',
            'path' => storage_path('logs/va/token.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'create_va' => [
            'driver' => 'daily',
            'path' => storage_path('logs/va/create_va.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'update_va' => [
            'driver' => 'daily',
            'path' => storage_path('logs/va/update_va.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'check_va' => [
            'driver' => 'daily',
            'path' => storage_path('logs/va/check_va.log'),
            'level' => 'info',
            'days' => 0,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];
