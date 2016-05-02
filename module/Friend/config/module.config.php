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
            \Friend\Service\SuggestedFriendServiceInterface::class => \Friend\Service\SuggestedFriendService::class,
        ],
        'invokables' => [
            \Friend\Delegator\FriendServiceDelegatorFactory::class =>
                \Friend\Delegator\FriendServiceDelegatorFactory::class,

            \Friend\Service\SuggestedFriendServiceFactory::class   =>
                \Friend\Service\SuggestedFriendServiceFactory::class,
        ],
        'factories'  => [
            \Friend\Service\FriendService::class          => \Friend\Service\FriendServiceFactory::class,
            \Friend\Service\SuggestedFriendService::class => \Friend\Service\SuggestedFriendServiceFactory::class,
        ],
        'delegators' => [
            \Friend\Service\FriendService::class          => [
                \Friend\Delegator\FriendServiceDelegatorFactory::class,
            ],
            \Friend\Service\SuggestedFriendService::class => [
                \Friend\Delegator\SuggestedFriendServiceDelegatorFactory::class
            ],
        ],
    ],
];
