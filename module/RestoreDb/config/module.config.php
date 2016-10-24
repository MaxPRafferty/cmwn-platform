<?php

return [
    'service_manager' => [
        'aliases' => [
            \RestoreDb\Service\RestoreDbServiceInterface::class => \RestoreDb\Service\RestoreDbService::class,
        ],
        'invokables' => [
            \RestoreDb\Delegator\RestoreDbDelegatorFactory::class =>
                \RestoreDb\Delegator\RestoreDbDelegatorFactory::class,
        ],
        'factories' => [
            \RestoreDb\Service\RestoreDbService::class => \RestoreDb\Service\RestoreDbServiceFactory::class,
            \RestoreDb\Listener\CheckConfigListener::class => \RestoreDb\Listener\CheckConfigListenerFactory::class,
        ],
        'delegators' => [
            \RestoreDb\Service\RestoreDbService::class => [\RestoreDb\Delegator\RestoreDbDelegatorFactory::class],
        ],
    ],
    'shared-listeners' => [
        \RestoreDb\Listener\CheckConfigListener::class,
    ],
];
