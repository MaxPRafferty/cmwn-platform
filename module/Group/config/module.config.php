<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Group\Service'                                 => \Group\Service\GroupService::class,
            'Group\GroupService'                            => \Group\Service\GroupService::class,
            \Group\Service\UserGroupServiceInterface::class => \Group\Service\UserGroupService::class,
            \Group\Service\GroupServiceInterface::class     => \Group\Service\GroupService::class,
        ],
        'factories'  => [
            \Group\Delegator\GroupDelegatorFactory::class            =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Group\Delegator\UserGroupServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Group\Service\GroupService::class                       => \Group\Service\GroupServiceFactory::class,
            \Group\Service\UserGroupService::class                   => \Group\Service\UserGroupServiceFactory::class,
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
    \Rule\Provider\Service\BuildProviderFromConfigFactory::class => [
        \Group\Rule\Provider\GroupTypesProvider::class => [
            \Group\Service\GroupServiceInterface::class,
        ],

        \Group\Rule\Provider\ChildGroupTypesProvider::class      => [
            \Group\Service\GroupServiceInterface::class,
        ],
    ],

    'providers' => [
        'shared' => [
            \Group\Rule\Provider\GroupTypesProvider::class       => false,
            \Group\Rule\Provider\ChildGroupTypesProvider::class => false,
        ]
    ]
];
