<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'org-types-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'org-types-hal-link',
            'name'                => 'All org type links on super me entity',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.all.orgs',
                            \Security\Rule\Provider\RoleProvider::PROVIDER_NAME
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
                        'name'    => \Api\Rule\Action\AddTypeLinksAction::class,
                        'options' => [
                            \Api\Links\OrgLink::class,
                            'org-types',
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Security\Rule\Provider\RoleProvider::class,
                \Org\Rule\Provider\OrgTypesProvider::class,
            ],
        ],

        'org-user-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'org-user-hal-link',
            'name'                => 'Attach the org-user hal links for entity if it has permissions',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'view.org.users',
                            \Api\Rule\Provider\ActiveUserOrgRoleProvider::class,
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
                            \Api\Links\OrgUserLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\ActiveUserOrgRoleProvider::class,
            ],
        ],

        'import-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'import-hal-link',
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
                            'import',
                            \Api\Rule\Provider\ActiveUserGroupRoleProvider::class,
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
                            \Api\Links\ImportLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME
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

    'specifications' => [
        'factories' => [
            'org-types-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'org-user-hal-link'    => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'import-hal-link'   => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
