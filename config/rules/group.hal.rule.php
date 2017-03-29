<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'group-reset-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'group-reset-hal-link',
            'name'                => 'Attaches group reset hal links',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'reset.group.code',
                            \Api\Rule\Provider\ActiveUserGroupRoleProvider::PROVIDER_NAME,
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
                            \Api\Links\GroupResetLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\ActiveUserGroupRoleProvider::class,
            ],
        ],

        'group-user-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'group-user-hal-link',
            'name'                => 'Attaches the group users hal link',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.group.users',
                            \Api\Rule\Provider\ActiveUserGroupRoleProvider::PROVIDER_NAME,
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
                            \Api\Links\GroupUserLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\ActiveUserGroupRoleProvider::class,
            ],
        ],

        'group-types-hal-link' => [
            //super me entity group type links
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'group-types-hal-link',
            'name'                => 'Attaches the group type hal links for an entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.all.groups',
                            \Security\Rule\Provider\RoleProvider::PROVIDER_NAME,
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
                        'name'    => \Api\Rule\Action\AddTypeLinksAction::class,
                        'options' => [
                            \Api\Links\GroupLink::class,
                            'group-types',
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Security\Rule\Provider\RoleProvider::class,
                \Group\Rule\Provider\GroupTypesProvider::class,
            ],
        ],

        'org-group-types-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'org-group-types-hal-link',
            'name'                => 'Attaches the group hal links for an org entity for all types of groups it has',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
                        'options' => [
                            \Org\OrganizationInterface::class,
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
                        'name'    => \Api\Rule\Action\AddTypeLinksAction::class,
                        'options' => [
                            \Api\Links\GroupLink::class,
                            'org-group-types',
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Org\Rule\Provider\OrgGroupTypesProvider::class,
            ],
        ],

        'child-group-types-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'child-group-types-hal-link',
            'name'                => 'Attaches the child group hal links for a group entity',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
                        'options' => [
                            \Group\GroupInterface::class,
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
                        'name'    => \Api\Rule\Action\AddTypeLinksAction::class,
                        'options' => [
                            \Api\Links\GroupLink::class,
                            'child-group-types',
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Group\Rule\Provider\ChildGroupTypesProvider::class,
            ],
        ],
        'group-address-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'group-address-hal-link',
            'name'                => 'Attaches the group address hal links for a group entity',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
                        'options' => [
                            \Group\GroupInterface::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.all.group.addresses',
                            \Api\Rule\Provider\ActiveUserGroupRoleProvider::PROVIDER_NAME
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
                            \Api\Links\GroupAddressLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\ActiveUserGroupRoleProvider::class,
            ],
        ],
    ],
    'specifications'                                                => [
        'factories' => [
            'group-reset-hal-link'       => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'group-types-hal-link'       => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'org-group-types-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'child-group-types-hal-link' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
