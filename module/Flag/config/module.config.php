<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Flag\Service\FlagServiceInterface::class => \Flag\Service\FlagService::class,
            \Flag\FlagInterface::class                => \Flag\Flag::class,
        ],
        'factories'  => [
            \Flag\Flag::class                           =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flag\Delegator\FlagDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flag\Service\FlagService::class            => \Flag\Service\FlagServiceFactory::class,
            \Flag\FlagHydrator::class                   => \Flag\FlagHydratorFactory::class,
        ],
        'delegators' => [
            \Flag\Service\FlagService::class => [
                \Flag\Delegator\FlagDelegatorFactory::class,
            ],
        ],
    ],
];
