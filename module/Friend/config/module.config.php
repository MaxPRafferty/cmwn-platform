<?php
return [
    'validators' => [
        'factories' => [
            \Friend\AttachFriendValidator::class => \Friend\AttachFriendValidatorFactory::class,
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
