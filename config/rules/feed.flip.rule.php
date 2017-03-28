<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'flip-feed' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'flip-feed',
            'name'                => 'Create feed when user earns a flip',
            'when'                => 'attach.flip.post',
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
                    'options' => ['feedable', 'flip'],
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
            'flip-feed'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
