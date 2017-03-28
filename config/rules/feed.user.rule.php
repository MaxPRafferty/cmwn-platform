<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'user-feed' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'user-feed',
            'name'                => 'Attach feed to user',
            'when'                => 'create.feed.post',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Always satisfied
                    \Rule\Rule\Basic\AlwaysSatisfiedRule::class,
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'name'    => \Feed\Rule\Action\InjectUserFeedAction::class,
                        'options' => [
                            \Feed\Service\FeedUserServiceInterface::class,
                            'feed',
                        ],
                    ],

                ],
            ],
            'providers'           => [
                [
                    'name'    => \Rule\Event\Provider\FromEventParamProvider::class,
                    'options' => ['feed', 'feed'],
                ],
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'user-feed' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
