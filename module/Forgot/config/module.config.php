<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Forgot\Service\ForgotServiceInterface::class => \Forgot\Service\ForgotService::class,
        ],
        'factories'  => [
            \Forgot\Delegator\ForgotServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Forgot\Service\ForgotService::class => \Forgot\Service\ForgotServiceFactory::class,
        ],
        'delegators' => [
            \Forgot\Service\ForgotService::class => [\Forgot\Delegator\ForgotServiceDelegatorFactory::class],
        ],
    ],
];
