<?php
return [
    'validators' => [
        'factories' => [
            \Friend\AttachFriendValidator::class => \Friend\AttachFriendValidatorFactory::class,
        ],
    ],

    'rules' => [
        'factories' => [
            \Friend\Rule\Rule\FriendStatusEqualsRule::class => \Rule\Rule\Service\BuildRuleFactory::class,
        ],
        'shared'    => [
            \Friend\Rule\Rule\FriendStatusEqualsRule::class => false,
        ],
    ],

    'service_manager' => [
        'aliases'    => [
            \Friend\Service\FriendServiceInterface::class          => \Friend\Service\FriendService::class,
        ],
        'factories'  => [
            \Friend\Delegator\FriendServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Friend\Service\FriendService::class          => \Friend\Service\FriendServiceFactory::class,
        ],
        'delegators' => [
            \Friend\Service\FriendService::class          => [
                \Friend\Delegator\FriendServiceDelegatorFactory::class,
            ],
        ],
    ],
];
