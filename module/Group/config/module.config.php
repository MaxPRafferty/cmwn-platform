<?php

return [
    'validators' => [
        'factories' => [
            \Group\RoleValidator::class => \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class,
        ],
    ],

    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Group\Service\GroupService::class        => [
            'GroupsTable',
        ],
        \Group\Service\UserGroupService::class    => [
            'UserGroupsTable',
            Zend\Db\Adapter\Adapter::class,
        ],
        \Group\Service\GroupAddressService::class => ['Table/GroupAddresses'],
        \Group\RoleValidator::class               => [
            \User\Service\UserServiceInterface::class,
            'Config',
        ],
    ],

    'service_manager' => [
        'aliases'    => [
            \Group\Service\UserGroupServiceInterface::class    => \Group\Service\UserGroupService::class,
            \Group\Service\GroupServiceInterface::class        => \Group\Service\GroupService::class,
            \Group\Service\GroupAddressServiceInterface::class => \Group\Service\GroupAddressService::class,
        ],
        'factories'  => [
            \Group\Delegator\GroupDelegatorFactory::class            =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Group\Delegator\GroupAddressDelegatorFactory::class     =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Group\Delegator\UserGroupServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'delegators' => [
            \Group\Service\GroupService::class        => [
                \Group\Delegator\GroupDelegatorFactory::class,
            ],
            \Group\Service\UserGroupService::class    => [
                \Group\Delegator\UserGroupServiceDelegatorFactory::class,
            ],
            \Group\Service\GroupAddressService::class => [
                \Group\Delegator\GroupAddressDelegatorFactory::class,
            ],
        ],
    ],

    \Search\ElasticHydrator::class => [
        'group' => [
            'hydrator'          => \Zend\Hydrator\ArraySerializable::class,
            'default_prototype' => \Group\Group::class,
            'class'             => \Group\GroupInterface::class,
        ],
    ],

    \Rule\Provider\Service\BuildProviderFromConfigFactory::class => [
        \Group\Rule\Provider\GroupTypesProvider::class => [
            \Group\Service\GroupServiceInterface::class,
        ],

        \Group\Rule\Provider\ChildGroupTypesProvider::class => [
            \Group\Service\GroupServiceInterface::class,
        ],
    ],

    'providers' => [
        'shared' => [
            \Group\Rule\Provider\GroupTypesProvider::class      => true,
            \Group\Rule\Provider\ChildGroupTypesProvider::class => true,
        ],
    ],
];
