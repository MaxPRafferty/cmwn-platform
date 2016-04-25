<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Flip\Service\FlipServiceInterface::class => \Flip\Service\FlipService::class,
        ],
        'invokables' => [
            \Flip\Flip::class                           => \Flip\Flip::class,
            \Flip\Delegator\FlipDelegatorFactory::class => \Flip\Delegator\FlipDelegatorFactory::class,
        ],
        'factories'  => [
            \Flip\Service\FlipService::class => \Flip\Service\FlipServiceFactory::class,
        ],
        'delegators' => [
            \Flip\Service\FlipService::class => [
                \Flip\Delegator\FlipDelegatorFactory::class,
            ],
        ],
    ],
];
