<?php
return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Game\Service\UserGameService::class => ['Table/UserGames'],
        \Game\Service\GameService::class     => ['Table/Games'],
        \Game\Service\SaveGameService::class => ['Table/user_saves']
    ],
    'service_manager'                                                 => [
        'aliases'    => [
            'Game\Service'                                => \Game\Service\GameService::class,
            \Game\Service\GameServiceInterface::class     => \Game\Service\GameService::class,
            \Game\Service\SaveGameServiceInterface::class => \Game\Service\SaveGameService::class,
            \Game\Service\UserGameServiceInterface::class => \Game\Service\UserGameService::class,
        ],
        'delegators' => [
            \Game\Service\SaveGameService::class => [
                \Game\Delegator\SaveGameDelegatorFactory::class,
            ],
            \Game\Service\GameService::class     => [
                \Game\Delegator\GameDelegatorFactory::class,
            ],
            \Game\Service\UserGameService::class => [
                \Game\Delegator\UserGameDelegatorFactory::class,
            ],
        ],
    ],
];
