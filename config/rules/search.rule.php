<?php
return [
    // TODO merge into one rule when rule stop in place
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'save-user-to-search' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'save-user-to-search',
            'name'                => 'Saves the user to search',
            'when'                => ['save.user.post', 'update.user.post'],
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Always satisfied
                    \Rule\Rule\Basic\AlwaysSatisfiedRule::class,
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    // Index the user document
                    [
                        'name'    => \Search\Rule\Action\SaveDocumentAction::class,
                        'options' => [
                            \Search\Service\ElasticServiceInterface::class,
                            'save_user',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                [
                    'name'    => \Rule\Event\Provider\FromEventParamProvider::class,
                    'options' => ['save_user', 'user'],
                ],
            ],
        ],
        'save-org-to-search'  => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'save-org-to-search',
            'name'                => 'Saves the org to search',
            'when'                => ['save.org.post', 'update.org.post'],
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Always satisfied
                    \Rule\Rule\Basic\AlwaysSatisfiedRule::class,
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    // Index the org document
                    [
                        'name'    => \Search\Rule\Action\SaveDocumentAction::class,
                        'options' => [
                            \Search\Service\ElasticServiceInterface::class,
                            'save_org',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                [
                    'name'    => \Rule\Event\Provider\FromEventParamProvider::class,
                    'options' => ['save_org', 'org'],
                ],
            ],
        ],
        'save-group-to-search'  => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'save-group-to-search',
            'name'                => 'Saves the group to search',
            'when'                => ['save.group.post', 'update.group.post'],
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Always satisfied
                    \Rule\Rule\Basic\AlwaysSatisfiedRule::class,
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    // Index the group document
                    [
                        'name'    => \Search\Rule\Action\SaveDocumentAction::class,
                        'options' => [
                            \Search\Service\ElasticServiceInterface::class,
                            'save_group',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                [
                    'name'    => \Rule\Event\Provider\FromEventParamProvider::class,
                    'options' => ['save_group', 'group'],
                ],
            ],
        ],

    ],

    'specifications' => [
        'factories' => [
            'save-user-to-search'  => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'save-org-to-search'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'save-group-to-search' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
