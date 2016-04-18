<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Group\Service'                                 => \Group\Service\GroupService::class,
            'Group\GroupService'                            => \Group\Service\GroupService::class,
            \Group\Service\UserGroupServiceInterface::class => \Group\Service\UserGroupService::class,
        ],
        'invokables' => [
            \Group\Delegator\GroupDelegatorFactory::class            => \Group\Delegator\GroupDelegatorFactory::class,
            \Group\Delegator\UserGroupServiceDelegatorFactory::class => \Group\Delegator\UserGroupServiceDelegatorFactory::class,
        ],
        'factories'  => [
            \Group\Service\GroupService::class     => \Group\Service\GroupServiceFactory::class,
            \Group\Service\UserGroupService::class => \Group\Service\UserGroupServiceFactory::class,
        ],
        'delegators' => [
            \Group\Service\GroupService::class     => [
                \Group\Delegator\GroupDelegatorFactory::class,
            ],
            \Group\Service\UserGroupService::class => [
                \Group\Delegator\UserGroupServiceDelegatorFactory::class,
            ],
        ],
    ],
];
