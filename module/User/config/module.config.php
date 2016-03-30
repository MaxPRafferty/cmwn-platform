<?php

return [
    'service_manager' => [
        'aliases' => [
            'User\Service' => 'User\Service\UserService',
        ],
        'invokables' => [
            'User\Delegator\UserDelegatorFactory'
        ],
        'factories' => [
            'User\Service\UserService' => 'User\Service\UserServiceFactory',
            'User\ServiceRandom\NameListener' =>'User\Service\RandomNameListenerFactory',
        ],
        'delegators' => [
            'User\Service\UserService' => [
                'User\Delegator\UserDelegatorFactory'
            ],
        ],
    ],
];
