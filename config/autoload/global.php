<?php
$dbName = getenv('IMAGE_LINK');
$dbName = empty($dbName) ? 'media.changemyworldnow.com/f' : $dbName;

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
