<?php
$dbName = getenv('IMAGE_LINK');
$dbName = empty($dbName) ? 'api.changemyworldnow.com' : $dbName;

return [
    'log' => [
        'Log\App' => [
            'writers' => [
                ['name' => 'noop',],
            ],
        ],
    ],

    'options' => [
        'image_domain' => $dbName,
    ],
];
