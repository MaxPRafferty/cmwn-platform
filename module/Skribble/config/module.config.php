<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Skribble\Service\SkribbleServiceInterface::class => \Skribble\Service\SkribbleService::class,
        ],
        'invokables' => [
            \Skribble\Delegator\SkribbleServiceDelegatorFactory::class =>
                \Skribble\Delegator\SkribbleServiceDelegatorFactory::class,
        ],
        'factories'  => [
            \Skribble\Service\SkribbleService::class => \Skribble\Service\SkribbleServiceFactory::class,
        ],
        'delegators' => [
            \Skribble\Service\SkribbleService::class => [
                \Skribble\Delegator\SkribbleServiceDelegatorFactory::class,
            ],
        ],
    ],
];
