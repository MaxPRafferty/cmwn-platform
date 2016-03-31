<?php

return [
    'service_manager' => [
        'invokables' => [
            'Forgot\Delegator\ForgotServiceDelegatorFactory' => 'Forgot\Delegator\ForgotServiceDelegatorFactory',
        ],
        'factories' => [
            'Forgot\Service\ForgotService' => 'Forgot\Service\ForgotServiceFactory',
        ],
        'delegators' => [
            'Forgot\Service\ForgotService' => ['Forgot\Delegator\ForgotServiceDelegatorFactory'],
        ],
    ],
];
