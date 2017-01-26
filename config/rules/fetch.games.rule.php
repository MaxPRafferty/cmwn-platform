<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'fetch-games-rule' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'fetch-games-rule',
            'name'                => 'adds necessary where conditions while fetching all games based on permissions',
            'when'                => ['fetch.all.games', 'fetch.game'],
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Has permission to earn flips
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.deleted.games',
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Rule\Event\Action\SetEventParamAction::class,
                        'options' => [
                            'show_deleted',
                            true
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\RoleProvider::class,
                \Rule\Event\Provider\EventProvider::class
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'fetch-games-rule' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
