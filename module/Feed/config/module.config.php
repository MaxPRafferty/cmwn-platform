<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Feed\Service\FeedService::class => ['Table/Feed'],
        \Feed\Service\FeedUserService::class => ['Table/UserFeed'],
    ],

    'actions' => [
        'factories' => [
            \Feed\Rule\Action\InjectFeedAction::class        => \Rule\Action\Service\BuildActionFactory::class,
            \Feed\Rule\Action\InjectUserFeedAction::class    => \Rule\Action\Service\BuildActionFactory::class,
        ],
        'shared'    => [
            \Feed\Rule\Action\InjectFeedAction::class        => false,
            \Feed\Rule\Action\InjectUserFeedAction::class    => false,
        ],
    ],

    'providers' => [
        'factories' => [
            \Feed\Rule\Provider\FeedFromFeedableProvider::class => \Rule\Provider\Service\BuildProviderFactory::class,
        ],
        'shared'    => [
            \Feed\Rule\Provider\FeedFromFeedableProvider::class => false,
        ],
    ],

    'service_manager' => [
        'aliases' => [
            \Feed\Service\FeedServiceInterface::class => \Feed\Service\FeedService::class,
            \Feed\Service\FeedUserServiceInterface::class => \Feed\Service\FeedUserService::class,
        ],
        'delegators' => [
            \Feed\Service\FeedService::class => [
                \Feed\Delegator\FeedDelegatorFactory::class
            ],
            \Feed\Service\FeedUserService::class => [
                \Feed\Delegator\FeedUserDelegatorFactory::class
            ]
        ]
    ],
];
