<?php

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
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
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
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\FlipLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\UserLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\PasswordLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
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
            'name'                => 'Attaches the feed hal links for a me entity if it has permissions',
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
                            \Api\Rule\Provider\UserRelationshipProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
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
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\UserRelationshipProvider::class,
            ],
        ],

        'user-feed-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'user-feed-hal-link',
            'name'                => 'Attaches the user feed hal links for a me entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.user.feed',
                            \Api\Rule\Provider\UserRelationshipProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
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
                            \Api\Links\UserFeedLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\UserRelationshipProvider::class,
            ],
        ],

        'user-flip-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'user-flip-hal-link',
            'name'                => 'Attaches the user flip hal links for a me entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'create.user.flip',
                            \Api\Rule\Provider\UserRelationshipProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
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
                            \Api\Links\UserFlipLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\UserRelationshipProvider::class,
            ],
        ],

        'user-name-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'user-name-hal-link',
            'name'                => 'Attaches the user name hal links for a me entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'pick.username',
                            \Api\Rule\Provider\UserRelationshipProvider::PROVIDER_NAME,
                        ],
                    ],
                    // and entity is a me entity
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
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
                            \Api\Links\UserNameLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
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
            'name'                => 'Attaches the flag hal links for a me entity if it has permissions',
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
                            \Api\Rule\Provider\UserRelationshipProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
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
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
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
            'name'                => 'Attaches the save game hal links for a me entity if it has permissions',
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
                            \Api\Rule\Provider\UserRelationshipProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
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
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
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
            'me-hal-links'       => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'feed-hal-link'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'user-feed-hal-link' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'flag-hal-link'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'save-game-hal-link' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
