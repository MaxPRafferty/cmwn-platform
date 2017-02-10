<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Org\Service\OrganizationService::class => [
            'OrganizationsTable',
        ],
    ],

    'service_manager' => [
        'aliases'    => [
            \Org\Service\OrganizationServiceInterface::class => \Org\Service\OrganizationService::class,
        ],
        'factories'  => [
            \Org\Delegator\OrganizationDelegatorFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

        ],
        'delegators' => [
            \Org\Service\OrganizationService::class => [
                \Org\Delegator\OrganizationDelegatorFactory::class,
            ],
        ],
    ],

    \Search\ElasticHydrator::class                               => [
        'organization' => [
            'hydrator'          => \Zend\Hydrator\ArraySerializable::class,
            'default_prototype' => \Org\Organization::class,
            'class'             => \Org\OrganizationInterface::class,
        ],
    ],

    \Rule\Provider\Service\BuildProviderFromConfigFactory::class => [
        \Org\Rule\Provider\OrgTypesProvider::class      => [
            \Org\Service\OrganizationServiceInterface::class,
        ],
        \Org\Rule\Provider\OrgGroupTypesProvider::class => [
            \Org\Service\OrganizationServiceInterface::class,
        ],
    ],

    'providers' => [
        'shared' => [
            \Org\Rule\Provider\OrgTypesProvider::class      => true,
            \Org\Rule\Provider\OrgGroupTypesProvider::class => true,
        ],
    ],
];
