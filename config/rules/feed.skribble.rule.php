<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'skribble-feed' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'skribble-feed',
            'name'                => 'Create feed when user earns a flip',
            'when'                => ['create.skribble.post', 'update.skribble.post'],
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    [
                        'name'    => \Skribble\Rule\Rule\SkribbleStatusEqualsRule::class,
                        'options' => [
                            'feedable'
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
                    'options' => ['user', 'user'],
                ],
                [
                    'name'    => \Rule\Event\Provider\FromEventParamProvider::class,
                    'options' => ['feedable', 'skribble'],
                ],
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'skribble-feed'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
