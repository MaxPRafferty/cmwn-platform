<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'game-feed' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'game-feed',
            'name'                => 'Create feed when new game is added',
            'when'                => ['create.game.post', 'update.game.post'],
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    [
                        'name'    => \Game\Rule\Rule\GameComingSoonRule::class,
                        'options' => [
                            'feedable',
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Feed\Rule\Action\InjectFeedAction::class,
                        'options' => [
                            \Feed\Service\FeedServiceInterface::class,
                            'user',
                            'feedable',
                        ],
                    ],

                ],
            ],
            'providers'           => [
                [
                    'name'    => \Rule\Event\Provider\FromEventParamProvider::class,
                    'options' => ['feedable', 'game'],
                ],
                [
                    'name'    => \Rule\Event\Provider\FromEventParamProvider::class,
                    'options' => ['user', 'user'],
                ],
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'game-feed'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
