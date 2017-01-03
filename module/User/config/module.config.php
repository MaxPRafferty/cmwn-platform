<?php

return [
    'validators'       => [
        'factories' => [
            \User\UpdateUsernameValidator::class => \User\UpdateUsernameValidatorFactory::class,
            \User\UpdateEmailValidator::class    => \User\UpdateEmailValidatorFactory::class,
        ],
    ],
    'service_manager'  => [
        'aliases'    => [
            'User\Service'                            => \User\Service\UserService::class,
            \User\Service\UserServiceInterface::class => \User\Service\UserService::class,
        ],
        'factories'  => [
            \User\Delegator\UserDelegatorFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \User\Service\UserService::class            => \User\Service\UserServiceFactory::class,
            \User\Service\RandomNameListener::class     => \User\Service\RandomNameListenerFactory::class,
            \User\UpdateUsernameValidator::class        => \User\UpdateUsernameValidatorFactory::class,
        ],
        'delegators' => [
            \User\Service\UserService::class => [
                \User\Delegator\UserDelegatorFactory::class,
            ],
        ],
    ],
    'shared-listeners' => [
//        \User\Service\RandomNameListener::class,
    ],
];
