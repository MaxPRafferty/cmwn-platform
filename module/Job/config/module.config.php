<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Job\Service' => \Job\Service\JobService::class,
            \Job\Service\JobServiceInterface::class => \Job\Service\JobService::class,
        ],
        'factories'  => [
            \Job\Processor\JobRunner::class => \Job\Processor\JobRunnerFactory::class,
        ],
        'invokables' => [
            \Job\Service\JobService::class => \Job\Service\JobService::class,
        ],
        'abstract_factories' => [
            \Job\Aws\Sqs\SqsJobServiceAbstractFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            'Job\Controller' => \Job\Controller\WorkerControllerFactory::class,
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
