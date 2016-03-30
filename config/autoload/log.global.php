<?php
return [
    'log' => [
        'Log\App' => [
            'writers' => [
                [
                    'name'     => 'stream',
                    'priority' => 1000,
                    'options'  => [
                        'stream' => realpath(__DIR__ . '/../../') . '/data/logs/app.log',
                    ],
                ],
            ],
        ],
    ],
];

