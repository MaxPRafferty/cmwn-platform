<?php

return [
    'service_manager' => [
        'invokables' => [
            \Forgot\Delegator\ForgotServiceDelegatorFactory::class =>
                \Forgot\Delegator\ForgotServiceDelegatorFactory::class,
        ],
        'factories'  => [
            \Forgot\Service\ForgotService::class => \Forgot\Service\ForgotServiceFactory::class,
        ],
        'delegators' => [
            \Forgot\Service\ForgotService::class => [\Forgot\Delegator\ForgotServiceDelegatorFactory::class],
        ],
    ],
];
