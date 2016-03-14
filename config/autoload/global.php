<?php
return [
    'log' => [
        'Log\App' => [
            'writers' => [
                ['name' => 'noop'],
            ],
        ],
    ],

    'zf-mvc-auth' => [
        'authentication' => [
            'map' => [
                'Api\\V1' => 'user',
            ],
        ],
    ],
];
