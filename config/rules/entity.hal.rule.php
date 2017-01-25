<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'username-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'username-hal-link',
            'name'                => 'Attaches the username hal link for a child entity if it has permissions',
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
                            \Api\Rule\Provider\UserRelationshipProvider::class,
                        ],
                    ],
                    [
                        'name'    => \User\Rule\TypeRule::class,
                        'options' => [
                            \User\UserInterface::TYPE_CHILD,
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

        'friend-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'friend-hal-link',
            'name'                => 'Attach the friend hal link for an entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'can.friend',
                            \Api\Rule\Provider\UserRelationshipProvider::class,
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
                            \Api\Links\FriendLink::class,
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

        'skribble-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'skribble-hal-link',
            'name'                => 'Attaches the skribble hal links for an entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.skribble',
                            \Api\Rule\Provider\UserRelationshipProvider::class,
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
                            \Api\Links\SkribbleLink::class,
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

        'forgot-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'forgot-hal-link',
            'name'                => 'Attaches the forgot hal links for an entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Rule\Rule\Object\IsTypeRule::class,
                        'options' => [
                            \Api\V1\Rest\User\MeEntity::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                        'operator' => 'not',
                    ],
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'adult.code',
                            \Api\Rule\Provider\UserRelationshipProvider::class,
                        ],
                    ],
                    [
                        'name'    => \User\Rule\TypeRule::class,
                        'options' => [
                            \User\UserInterface::TYPE_ADULT,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
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
                            \Api\Links\ForgotLink::class,
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

        'reset-password-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'reset-password-hal-link',
            'name'                => 'Attaches the hal links for an entity if it has permissions',
            'when'                => ['renderEntity', 'renderCollection.entity'],
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'child.code',
                            \Api\Rule\Provider\UserRelationshipProvider::class,
                        ],
                    ],
                    [
                        'name'    => \User\Rule\TypeRule::class,
                        'options' => [
                            \User\UserInterface::TYPE_CHILD,
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
                            \Api\Links\ResetLink::class,
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
            'username-hal-link' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'friend-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'skribble-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'forgot-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'reset-password-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
