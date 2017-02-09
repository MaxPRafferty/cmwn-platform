<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'sa-settings-hal-link' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'sa-settings-hal-link',
            'name'                => 'Sa settings hal link on super me entity',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'sa.settings',
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
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Sa\Links\SuperAdminSettingsLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\GroupLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                            false,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\OrgLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                            false,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Security\Rule\Provider\RoleProvider::class,
            ],
        ],
        'super-hal-link'       => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'super-hal-link',
            'name'                => 'Super hal link on adult entities who can view super users',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity has permissions
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'get.super.user',
                            \Security\Rule\Provider\RoleProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \User\Rule\TypeRule::class,
                        'options' => [
                            \User\UserInterface::TYPE_ADULT,
                            \Api\Rule\Provider\RealEntityFromEventProvider::PROVIDER_NAME,
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
                            \Api\Links\SuperLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
                \Api\Rule\Provider\RealEntityFromEventProvider::class,
                \Security\Rule\Provider\RoleProvider::class,
            ],
        ],
    ],
    'specifications'                                                => [
        'sa-settings-hal-link' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        'super-flag-hal-link'  => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,

    ],
];
