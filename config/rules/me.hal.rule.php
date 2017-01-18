<?php
return [];
return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'me-hal-links' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'me-hal-links',
            'name'                => 'Attaches the hal links for a me entity',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity is a me entity
                    [
                        'name'    => \Rule\Rule\Object\IsTypeRule::class,
                        'options' => [
                            \Api\V1\Rest\User\MeEntity::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    //Add a hal link to the entity
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\GameLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\FlipLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\UserLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\PasswordLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
            ],
        ],

        'feed-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'feed-hal-link',
            'name'                => 'Attaches the hal links for an entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.feed',
                            \Api\Rule\Provider\UserRelationshipProvider::class,
                        ],
                    ],
                    [
                        'name'    => \Rule\Rule\Object\IsTypeRule::class,
                        'options' => [
                            \Api\V1\Rest\User\MeEntity::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    //Add a hal link to the entity
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\FeedLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\UserRelationshipProvider::class,
            ],
        ],

        'flag-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'flag-hal-link',
            'name'                => 'Attaches the hal links for an entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'flag.image',
                            \Api\Rule\Provider\UserRelationshipProvider::class,
                        ],
                    ],
                    [
                        'name'    => \Rule\Rule\Object\IsTypeRule::class,
                        'options' => [
                            \Api\V1\Rest\User\MeEntity::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    //Add a hal link to the entity
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\FlagLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\UserRelationshipProvider::class,
            ],
        ],

        'save-game-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'save-game-hal-link',
            'name'                => 'Attaches the hal links for an entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'save.game',
                            \Api\Rule\Provider\UserRelationshipProvider::class,
                        ],
                    ],
                    [
                        'name'    => \Rule\Rule\Object\IsTypeRule::class,
                        'options' => [
                            \Api\V1\Rest\User\MeEntity::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    //Add a hal link to the entity
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\SaveGameLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\UserRelationshipProvider::class,
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'me-hal-links' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'feed-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'flag-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'save-game-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
