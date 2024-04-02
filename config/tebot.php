<?php

return [
    'default' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => env('TEBOT_KEY', null),
    ],

    'check_no_skrd' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => '080f46c65f39bcbbf0c824b8fa38a3db'
    ],

    'log_skrd' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => 'a25eb0f55d3c36f511307d49ede6b890'
    ],

    'log_skrd_local' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => 'a8a9957bfe15a25d3ef02aefafe4f0a8'
    ],

    'log_va' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => 'c77effe5215b60bb22af4e364eaf6a27'
    ],

    'log_qris' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => 'e1504e7b8718f8fd6ebe195720fc1339'
    ],
];
