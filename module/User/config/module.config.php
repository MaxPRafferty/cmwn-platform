<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \User\Service\UserService::class        => ['UsersTable'],
        \User\Service\RandomNameListener::class => ['NamesTable'],
    ],
    'validators'                                                      => [
        'factories' => [
            \User\Validator\UpdateUsernameValidator::class => \User\Validator\UpdateUsernameValidatorFactory::class,
            \User\Validator\UpdateEmailValidator::class    => \User\Validator\UpdateEmailValidatorFactory::class,
        ],
    ],
    'service_manager'                                                 => [
        'aliases'    => [
            'User\Service'                            => \User\Service\UserService::class,
            \User\Service\UserServiceInterface::class => \User\Service\UserService::class,
        ],
        'factories'  => [
            \User\Delegator\UserDelegatorFactory::class    => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \User\Validator\UpdateUsernameValidator::class => \User\Validator\UpdateUsernameValidatorFactory::class,
        ],
        'delegators' => [
            \User\Service\UserService::class => [
                \User\Delegator\UserDelegatorFactory::class,
            ],
        ],
    ],
    'shared-listeners'                                                => [
        \User\Service\RandomNameListener::class,
    ],

    \Search\ElasticHydrator::class => [
        'user' => [
            'hydrator'          => \User\UserHydrator::class,
            'default_prototype' => null,
            'class'             => \User\UserInterface::class,
        ],
    ],

    'rules' => [
        'factories' => [
            \User\Rule\TypeRule::class => \Rule\Rule\Service\BuildRuleFactory::class,
        ],

        'shared' => [
            \User\Rule\TypeRule::class => false,
        ],
    ],
];
