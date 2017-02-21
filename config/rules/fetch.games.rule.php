<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'fetch-all-games-rule' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'fetch-all-games-rule',
            'name'                => 'adds necessary where conditions while fetching all games based on permissions',
            'when'                => 'fetch.all.games',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // if the deleted param in the event is true
                    [
                        'name'    => \Rule\Event\Rule\EventParamMatches::class,
                        'options' => [
                            'show_deleted',
                            true,
                        ],
                    ],

                    // And they do not have permission
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.deleted.games',
                        ],
                        'operator' => 'not',
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Api\Rule\Action\ThrowException::class,
                        'options' => [
                            \Application\Exception\NotAuthorizedException::class,
                            'Unauthorized',
                            403,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\RoleProvider::class,
                \Rule\Event\Provider\EventProvider::class
            ],
        ],

        'fetch-game-rule' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'fetch-game-rule',
            'name'                => 'adds necessary where conditions while fetching game based on permissions',
            'when'                => 'fetch.game',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Has permission to view deleted games
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
            'fetch-all-games-rule' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'fetch-game-rule' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
