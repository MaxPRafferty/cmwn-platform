<?php

return [
    'user-table-config' => [
        'class' => ''
    ],

    'service_manager' => [
        'aliases' => [
            'User\Service' => 'User\Service\UserService'
        ],
        'invokables' => [
            'User\Delegator\UserDelegatorFactory'
        ],
        'factories' => [
            'User\Service\UserService' => 'User\Service\UserServiceFactory'
        ],
        'delegators' => [
            'User\Service\UserService' => [
                'User\Delegator\UserDelegatorFactory'
            ],
        ],
    ],
];
