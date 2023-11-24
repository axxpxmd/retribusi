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

    'log_skrd_testing' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => '66f02dd04a3a08eef46bb335945ab4ae'
    ],

    'log_skrd' => [
        'name' => env('TEBOT_NAME', 'TEBOT'),
        'url' => env('TEBOT_URL', 'localhost'),
        'key' => 'a25eb0f55d3c36f511307d49ede6b890'
    ],
];
