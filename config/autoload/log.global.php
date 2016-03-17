<?php
return [
    'log' => [
        'Log\App' => [
            'writers' => [
                [
                    'name'     => 'noop',
                    'priority' => 1000,
                ],
            ],
        ],
    ],
];
