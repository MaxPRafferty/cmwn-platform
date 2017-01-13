<?php

return [
    'shared-listeners' => [
        'Feed\Listener\InjectFeedListener' => \Feed\Listener\InjectFeedListener::class
    ],
    'service_manager' => [
        'aliases' => [
            \Feed\Service\FeedServiceInterface::class => \Feed\Service\FeedService::class,
            \Feed\Service\FeedUserServiceInterface::class => \Feed\Service\FeedUserService::class,
        ],
        'factories' => [
            \Feed\Service\FeedService::class => \Feed\Service\FeedServiceFactory::class,
            \Feed\Service\FeedUserService::class => \Feed\Service\FeedUserServiceFactory::class,
            \Feed\Listener\InjectFeedListener::class => \Feed\Listener\InjectFeedListenerFactory::class,
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
