<?php
return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Game\Service\UserGameService::class => ['Table/UserGames'],
    ],
    'service_manager' => [
        'aliases'    => [
            'Game\Service'                                => \Game\Service\GameService::class,
            \Game\Service\GameServiceInterface::class     => \Game\Service\GameService::class,
            \Game\Service\SaveGameServiceInterface::class => \Game\Service\SaveGameService::class,
            \Game\Service\UserGameServiceInterface::class => \Game\Service\UserGameService::class
        ],
        'factories'  => [
            \Game\Delegator\SaveGameDelegatorFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Game\Service\GameService::class                => \Game\Service\GameServiceFactory::class,
            \Game\Service\SaveGameService::class            => \Game\Service\SaveGameServiceFactory::class,
            \Game\Delegator\UserGameDelegatorFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'delegators' => [
            \Game\Service\SaveGameService::class => [
                \Game\Delegator\SaveGameDelegatorFactory::class,
            ],
            \Game\Service\GameService::class => [
                \Game\Delegator\GameDelegatorFactory::class,
            ],
            \Game\Service\UserGameService::class => [
                \Game\Delegator\UserGameDelegatorFactory::class,
            ],
        ],
    ],
];
