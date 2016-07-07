<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Skribble\Service\SkribbleServiceInterface::class => \Skribble\Service\SkribbleService::class,
        ],
        'invokables' => [
            \Skribble\Delegator\SkribbleServiceDelegator::class => \Skribble\Delegator\SkribbleServiceDelegator::class,
        ],
        'factories'  => [
            \Skribble\Service\SkribbleService::class => \Skribble\Service\SkribbleServiceFactory::class,
        ],
        'delegators' => [
            \Skribble\Service\SkribbleService::class => [
                \Skribble\Delegator\SkribbleServiceDelegator::class,
            ],
        ],
    ],
];
