<?php

/**
 * Rules to unlock the GTC path
 *
 * The order of the games is:
 *
 * gtc-recycling-champion
 * gtc-priceless-pourer
 * gtc-fantastic-food-sharer
 * gtc-dynamic-diverter
 * gtc-master-sorter
 *
 * @@codingStandardsIgnoreStart
 */
return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'unlock-gtc-priceless-pourer' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'unlock-gtc-priceless-pourer',
            'name'                => 'Unlocks GTC Priceless Pourer when the user earns the Recycling Champion flip',
            'when'                => 'attach.flip.post',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // User has earned gtc-recycling-champion flip
                    [
                        'name'    => \Flip\Rule\Rule\EarnedFlip::class,
                        'options' => [
                            \Flip\Service\FlipUserServiceInterface::class,
                            'recycling-champion',
                            \Security\Rule\Provider\ActiveUserProvider::PROVIDER_NAME,
                        ],
                    ],

                    // User is currently not allowed to play gtc-priceless-pourer
                    [
                        'name'     => \Game\Rule\Rule\UserCanPlayGame::class,
                        'options'  => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-priceless-pourer',
                        ],
                        'operator' => 'not',
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Game\Rule\Action\AddGameToUserAction::class,
                        'options' => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-priceless-pourer',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\ActiveUserProvider::class,
            ],
        ],

        'unlock-gtc-fantastic-food-sharer' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'unlock-gtc-fantastic-food-sharer',
            'name'                => 'Unlocks GTC Fantastic Food Sharer when the user earns the Priceless Pourer filp',
            'when'                => 'attach.flip.post',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // User has earned gtc-priceless-pourer flip
                    [
                        'name'    => \Flip\Rule\Rule\EarnedFlip::class,
                        'options' => [
                            \Flip\Service\FlipUserServiceInterface::class,
                            'priceless-pourer',
                            \Security\Rule\Provider\ActiveUserProvider::PROVIDER_NAME,
                        ],
                    ],

                    // User is currently not allowed to play gtc-fantastic-food-sharer
                    [
                        'name'     => \Game\Rule\Rule\UserCanPlayGame::class,
                        'options'  => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-fantastic-food-sharer',
                        ],
                        'operator' => 'not',
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Game\Rule\Action\AddGameToUserAction::class,
                        'options' => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-fantastic-food-sharer',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\ActiveUserProvider::class,
            ],
        ],

        'unlock-gtc-dynamic-diverter' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'unlock-gtc-dynamic-diverter',
            'name'                => 'Unlocks GTC Dynamic Diverter game when the user earns the Fantastic Food Sharer Flip',
            'when'                => 'attach.flip.post',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // User has earned gtc-fantastic-food-sharer flip
                    [
                        'name'    => \Flip\Rule\Rule\EarnedFlip::class,
                        'options' => [
                            \Flip\Service\FlipUserServiceInterface::class,
                            'fantastic-food-sharer',
                            \Security\Rule\Provider\ActiveUserProvider::PROVIDER_NAME,
                        ],
                    ],

                    // User is currently not allowed to play gtc-dynamic-diverter
                    [
                        'name'     => \Game\Rule\Rule\UserCanPlayGame::class,
                        'options'  => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-dynamic-diverter',
                        ],
                        'operator' => 'not',
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Game\Rule\Action\AddGameToUserAction::class,
                        'options' => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-dynamic-diverter',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\ActiveUserProvider::class,
            ],
        ],

        'unlock-gtc-master-sorter' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'unlock-gtc-master-sorter',
            'name'                => 'Unlocks GTC Master Sorter game when the user earns the Dynamic Diverter flip',
            'when'                => 'attach.flip.post',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // User has earned gtc-dynamic-diverter flip
                    [
                        'name'    => \Flip\Rule\Rule\EarnedFlip::class,
                        'options' => [
                            \Flip\Service\FlipUserServiceInterface::class,
                            'dynamic-diverter',
                            \Security\Rule\Provider\ActiveUserProvider::PROVIDER_NAME,
                        ],
                    ],

                    // User is currently not allowed to play gtc-master-sorter
                    [
                        'name'     => \Game\Rule\Rule\UserCanPlayGame::class,
                        'options'  => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-master-sorter',
                        ],
                        'operator' => 'not',
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Game\Rule\Action\AddGameToUserAction::class,
                        'options' => [
                            \Game\Service\UserGameServiceInterface::class,
                            'gtc-master-sorter',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\ActiveUserProvider::class,
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'unlock-gtc-priceless-pourer'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'unlock-gtc-fantastic-food-sharer' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'unlock-gtc-dynamic-diverter'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
            'unlock-gtc-master-sorter'         => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
