<?php

return [
    'service_manager' => [
        'aliases'    => [
            'User\Service'                            => \User\Service\UserService::class,
            \User\Service\UserServiceInterface::class => \User\Service\UserService::class,
        ],
        'invokables' => [
            \User\Delegator\UserDelegatorFactory::class,
        ],
        'factories'  => [
            \User\Service\UserService::class        => \User\Service\UserServiceFactory::class,
            \User\Service\RandomNameListener::class => \User\Service\RandomNameListenerFactory::class,
        ],
        'delegators' => [
            \User\Service\UserService::class => [
                \User\Delegator\UserDelegatorFactory::class,
            ],
        ],
    ],
];
