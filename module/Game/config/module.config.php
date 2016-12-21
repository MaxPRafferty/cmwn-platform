<?php
return [
    'service_manager' => [
        'aliases'    => [
            'Game\Service'                                => \Game\Service\GameService::class,
            \Game\Service\GameServiceInterface::class     => \Game\Service\GameService::class,
            \Game\Service\SaveGameServiceInterface::class => \Game\Service\SaveGameService::class,
        ],
        'invokables' => [
            \Game\Delegator\SaveGameDelegatorFactory::class => \Game\Delegator\SaveGameDelegatorFactory::class,
        ],
        'factories'  => [
            \Game\Service\GameService::class     => \Game\Service\GameServiceFactory::class,
            \Game\Service\SaveGameService::class => \Game\Service\SaveGameServiceFactory::class,
        ],
        'delegators' => [
            \Game\Service\SaveGameService::class => [
                \Game\Delegator\SaveGameDelegatorFactory::class,
            ],
            \Game\Service\GameService::class => [
                \Game\Delegator\GameDelegatorFactory::class,
            ]
        ],
    ],
];
