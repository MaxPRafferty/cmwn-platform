<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Flip\Service\FlipServiceInterface::class     => \Flip\Service\FlipService::class,
            \Flip\Service\FlipUserServiceInterface::class => \Flip\Service\FlipUserService::class,
        ],
        'invokables' => [
            \Flip\Flip::class                           => \Flip\Flip::class,
            \Flip\EarnedFlip::class                     => \Flip\EarnedFlip::class,
            \Flip\Delegator\FlipDelegatorFactory::class => \Flip\Delegator\FlipDelegatorFactory::class,
        ],
        'factories'  => [
            \Flip\Service\FlipUserService::class => \Flip\Service\FlipUserServiceFactory::class,
            \Flip\Service\FlipService::class     => \Flip\Service\FlipServiceFactory::class,
        ],
        'delegators' => [
            \Flip\Service\FlipService::class => [
                \Flip\Delegator\FlipDelegatorFactory::class,
            ],
            \Flip\Service\FlipService::class => [
                \Flip\Delegator\FlipUserDelegatorFactory::class,
            ],
        ],
    ],
];
