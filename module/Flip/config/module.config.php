<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Flip\Service\FlipServiceInterface::class     => \Flip\Service\FlipService::class,
            \Flip\Service\FlipUserServiceInterface::class => \Flip\Service\FlipUserService::class,
        ],
        'factories'  => [
            \Flip\Flip::class                               => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flip\EarnedFlip::class                         => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flip\Delegator\FlipDelegatorFactory::class     => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flip\Delegator\FlipUserDelegatorFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flip\Service\FlipUserService::class            => \Flip\Service\FlipUserServiceFactory::class,
            \Flip\Service\FlipService::class                => \Flip\Service\FlipServiceFactory::class,
        ],
        'delegators' => [
            \Flip\Service\FlipService::class     => [
                \Flip\Delegator\FlipDelegatorFactory::class,
            ],
            \Flip\Service\FlipUserService::class => [
                \Flip\Delegator\FlipUserDelegatorFactory::class,
            ],
        ],
    ],
];
