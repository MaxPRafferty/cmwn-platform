<?php

return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'friend-feed' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'friend-feed',
            'name'                => 'Create feed when user friends other user',
            'when'                => 'attach.friend.post',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    [
                        'name' => \Friend\Rule\Rule\FriendStatusEqualsRule::class,
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
                    'options' => ['feedable', 'friend'],
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
            'friend-feed'      => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
