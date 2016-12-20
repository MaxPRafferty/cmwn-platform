<?php
return [
    'service_manager' => [
        'aliases'    => [
            'Game\Service'                                => \Game\Service\GameService::class,
            \Game\Service\GameServiceInterface::class     => \Game\Service\GameService::class,
            \Game\Service\SaveGameServiceInterface::class => \Game\Service\SaveGameService::class,
        ],
        'factories'  => [
            \Game\Delegator\SaveGameDelegatorFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Game\Service\GameService::class                => \Game\Service\GameServiceFactory::class,
            \Game\Service\SaveGameService::class            => \Game\Service\SaveGameServiceFactory::class,
        ],
        'delegators' => [
            \Game\Service\SaveGameService::class => [
                \Game\Delegator\SaveGameDelegatorFactory::class,
            ],
        ],
    ],
];
