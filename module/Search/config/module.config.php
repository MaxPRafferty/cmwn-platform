<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Search\ElasticHydrator::class => ['Config'],
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

    'actions' => [
        'factories' => [
            \Search\Rule\Action\SaveDocumentAction::class   => \Rule\Action\Service\BuildActionFactory::class,
            \Search\Rule\Action\DeleteDocumentAction::class => \Rule\Action\Service\BuildActionFactory::class,
        ],
        'shared'    => [
            \Search\Rule\Action\SaveDocumentAction::class   => false,
            \Search\Rule\Action\DeleteDocumentAction::class => false,
        ],
    ],

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
