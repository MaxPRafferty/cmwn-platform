<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Org\Service'                                    => \Org\Service\OrganizationService::class,
            'Organization\Service'                           => \Org\Service\OrganizationService::class,
            \Org\Service\OrganizationServiceInterface::class => \Org\Service\OrganizationService::class,
        ],
        'invokables' => [
            \Org\Delegator\OrganizationDelegatorFactory::class => \Org\Delegator\OrganizationDelegatorFactory::class,
        ],
        'factories'  => [
            \Org\Service\OrganizationService::class => \Org\Service\OrganizationServiceFactory::class,
        ],
        'delegators' => [
            \Org\Service\OrganizationService::class => [
                \Org\Delegator\OrganizationDelegatorFactory::class,
            ],
        ],
    ],
];
