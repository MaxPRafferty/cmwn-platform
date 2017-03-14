<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Address\Service\AddressService::class => ['Table/Addresses'],
    ],

    'validators' => [
        'factories' => [
            \Address\CountryStateValidator::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],

    'service_manager' => [
        'aliases' => [
            \Address\Service\AddressServiceInterface::class =>
                \Address\Service\AddressService::class,
        ],
        'factories' => [
            \Address\Delegator\AddressDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'delegators' => [
            \Address\Service\AddressService::class => [
                \Address\Delegator\AddressDelegatorFactory::class
            ],
        ]
    ],
];
