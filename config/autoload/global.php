<?php
return [
    'log' => [
        'Log\App' => [
            'writers' => [
                ['name' => 'noop'],
            ],
        ],
    ],

    'options' => [
        'image_domain' => 'https://media.changemyworldnow.com/f',
    ],

    'allow-reset' => false // allows the /restore endpoint to be called
];
