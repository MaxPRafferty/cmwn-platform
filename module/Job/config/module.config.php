<?php

return [
    'service_manager' => [
        'aliases' => [
            'Job\Service' => 'Job\Service\JobService'
        ],
        'factories' => [
            'Job\Processor\JobRunner' => 'Job\Processor\JobRunnerFactory',
        ],
        'invokables' => [
            'Job\Service\JobService' => 'Job\Service\JobService',
        ],
    ],

    'controllers' => [
        'factories' => [
            'Job\Controller' => 'Job\Controller\WorkerControllerFactory',
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'run-worker' => [
                    'options' => [
                        'route'    => 'worker --queue=',
                        'defaults' => [
                            'controller' => 'Job\Controller',
                            'action'     => 'work',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
