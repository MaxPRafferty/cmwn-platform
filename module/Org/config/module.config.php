<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Org\Service'                                    => \Org\Service\OrganizationService::class,
            'Organization\Service'                           => \Org\Service\OrganizationService::class,
            \Org\Service\OrganizationServiceInterface::class => \Org\Service\OrganizationService::class,
        ],
        'factories'  => [
            \Org\Delegator\OrganizationDelegatorFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Org\Service\OrganizationService::class => \Org\Service\OrganizationServiceFactory::class,
        ],
        'delegators' => [
            \Org\Service\OrganizationService::class => [
                \Org\Delegator\OrganizationDelegatorFactory::class,
            ],
        ],
    ],

    \Rule\Provider\Service\BuildProviderFromConfigFactory::class => [
        \Org\Rule\Provider\OrgTypesProvider::class => [
            \Org\Service\OrganizationServiceInterface::class,
        ],
        \Org\Rule\Provider\OrgGroupTypesProvider::class => [
            \Org\Service\OrganizationServiceInterface::class,
        ],
    ],

    'providers' => [
        'shared' => [
            \Org\Rule\Provider\OrgTypesProvider::class => true,
            \Org\Rule\Provider\OrgGroupTypesProvider::class => true,
        ],
    ],
];
