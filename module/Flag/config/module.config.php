<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Flag\Service\FlagServiceInterface::class => \Flag\Service\FlagService::class,
            \Flag\FlagInterface::class => \Flag\Flag::class,
        ],
        'invokables' => [
            \Flag\Flag::class =>\Flag\Flag::class,
            \Flag\Delegator\FlagDelegatorFactory::class => \Flag\Delegator\FlagDelegatorFactory::class,
        ],
        'factories'  => [
            \Flag\Service\FlagService::class => \Flag\Service\FlagServiceFactory::class,
            \Flag\FlagHydrator::class => \Flag\FlagHydratorFactory::class,
        ],
        'delegators' => [
            \Flag\Service\FlagService::class => [
                \Flag\Delegator\FlagDelegatorFactory::class
            ],
        ],
    ],
];
