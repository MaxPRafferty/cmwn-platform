<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'user-default-hal-links' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'user-default-hal-links',
            'name'                => 'Adds the default links to the user entity',
            'when'                => 'renderEntity',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // entity is a me entity
                    [
                        'name'    => \Api\Rule\Rule\EntityIsType::class,
                        'options' => [
                            \Api\V1\Rest\User\UserEntity::class,
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
                            \Api\Links\ProfileLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\UserImageLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                    [
                        'name'    => \Api\Rule\Action\AddHalLinkAction::class,
                        'options' => [
                            \Api\Links\ProfileLink::class,
                            \Api\Rule\Provider\EntityFromEventProvider::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Api\Rule\Provider\EntityFromEventProvider::class,
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
