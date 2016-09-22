<?php

return [
    'controllers' => [
        'factories' => [
            'SuggestCron\Controller' => \SuggestCron\Controller\SuggestCronControllerFactory::class,
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'suggest-cli' => [
                    'options' => [
                        'route'    => 'cron:suggest [--verbose|-v] [--debug|-d]',
                        'defaults' => [
                            'controller' => 'SuggestCron\Controller',
                            'action'     => 'suggestCron',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
