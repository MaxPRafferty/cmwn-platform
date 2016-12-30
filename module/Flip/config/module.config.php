<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Flip\Service\FlipUserService::class => ['Table/UserFlips'],
        \Flip\Service\FlipService::class     => ['FlipsTable'],
    ],

    'service_manager' => [
        'aliases'    => [
            \Flip\Service\FlipServiceInterface::class     => \Flip\Service\FlipService::class,
            \Flip\Service\FlipUserServiceInterface::class => \Flip\Service\FlipUserService::class,
        ],
        'factories'  => [
            \Flip\Delegator\FlipServiceDelegatorFactory::class     =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flip\Delegator\FlipUserServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'delegators' => [
            \Flip\Service\FlipService::class     => [
                \Flip\Delegator\FlipServiceDelegatorFactory::class,
            ],
            \Flip\Service\FlipUserService::class => [
                \Flip\Delegator\FlipUserServiceDelegatorFactory::class,
            ],
        ],
    ],
];
