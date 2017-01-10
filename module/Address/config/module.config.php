<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Address\Service\AddressService::class => ['Table/Addresses'],
        \Address\Service\GroupAddressService::class => ['Table/GroupAddresses']
    ],

    'service_manager' => [
        'aliases' => [
            \Address\Service\AddressServiceInterface::class =>
                \Address\Service\AddressService::class,
            \Address\Service\GroupAddressServiceInterface::class =>
                \Address\Service\GroupAddressService::class
        ],
        'factories' => [
            \Address\Delegator\AddressDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Address\Delegator\GroupAddressDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class
        ],

        'delegators' => [
            \Address\Service\AddressService::class => [
                \Address\Delegator\AddressDelegatorFactory::class
            ],
            \Address\Service\GroupAddressService::class => [
                \Address\Delegator\GroupAddressDelegatorFactory::class
            ]
        ]
    ],
];
