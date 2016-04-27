<?php

return [
    'service_manager' => [
        'aliases'   => [
            'Game\Service'                            => \Game\Service\GameService::class,
            \Game\Service\GameServiceInterface::class => \Game\Service\GameService::class,
        ],
        'factories' => [
            \Game\Service\GameService::class => \Game\Service\GameServiceFactory::class,
        ],
    ],
];
