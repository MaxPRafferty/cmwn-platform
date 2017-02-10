<?php

return [

    \Zend\Config\AbstractConfigFactory::class => [
        \Search\ElasticHydrator::class => [
            \Zend\Config\Config::class,
        ],
    ],

    'service_manager' => [
        'aliases'   => [
            \Search\Service\ElasticServiceInterface::class => \Search\Service\ElasticService::class,
            \Elasticsearch\Client::class                   => \Search\Service\ElasticService::class,
        ],
        'factories' => [
            \Search\Service\ElasticService::class => \Search\Service\ElasticServiceFactory::class,
        ],
    ],

    'actions'                      => [
        'factories' => [
            \Search\Rule\Action\SaveDocumentAction::class   => \Rule\Rule\Service\BuildRuleFactory::class,
            \Search\Rule\Action\DeleteDocumentAction::class => \Rule\Rule\Service\BuildRuleFactory::class,
        ],
    ],

    // TODO move to each module to make more SOLID
    \Search\ElasticHydrator::class => [
        'user' => [
            'hydrator'          => \User\UserHydrator::class,
            'default_prototype' => null,
            'class'             => \User\UserInterface::class,
        ],

        'group' => [
            'hydrator'          => \Zend\Hydrator\ArraySerializable::class,
            'default_prototype' => \Group\Group::class,
            'class'             => \Group\GroupInterface::class,
        ],

        'organization' => [
            'hydrator'          => \Zend\Hydrator\ArraySerializable::class,
            'default_prototype' => \Org\Organization::class,
            'class'             => \Org\OrganizationInterface::class,
        ],
    ],
];
