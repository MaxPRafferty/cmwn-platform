<?php
return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Game\Service\UserGameService::class => ['Table/UserGames'],
        \Game\Service\GameService::class     => ['Table/Games'],
        \Game\Service\SaveGameService::class => ['Table/user_saves'],
    ],

    'service_manager' => [
        'aliases'    => [
            'Game\Service'                                => \Game\Service\GameService::class,
            \Game\Service\GameServiceInterface::class     => \Game\Service\GameService::class,
            \Game\Service\SaveGameServiceInterface::class => \Game\Service\SaveGameService::class,
            \Game\Service\UserGameServiceInterface::class => \Game\Service\UserGameService::class,
        ],
        'delegators' => [
            \Game\Service\SaveGameService::class => [
                \Game\Delegator\SaveGameDelegatorFactory::class,
            ],
            \Game\Service\GameService::class     => [
                \Game\Delegator\GameDelegatorFactory::class,
            ],
            \Game\Service\UserGameService::class => [
                \Game\Delegator\UserGameDelegatorFactory::class,
            ],
        ],
    ],

    'input_filter_specs' => [
        'Game\Validator' => [
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'title',
                'description' => 'Title of the game',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'description',
                'description' => 'Description of the game',
            ],
            [
                'required'    => false,
                'allow_empty' => true,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\Boolean::class,
                        'options' => ['type' => \Zend\Filter\Boolean::TYPE_ALL],
                    ],
                ],
                'name'        => 'coming_soon',
                'description' => 'if the game is coming soon',
            ],
            [
                'required'    => false,
                'allow_empty' => true,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\Boolean::class,
                        'options' => ['type' => \Zend\Filter\Boolean::TYPE_ALL],
                    ],
                ],
                'name'        => 'desktop',
                'description' => 'Mark the game as desktop only',
            ],
            [
                'required'    => false,
                'allow_empty' => true,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\Boolean::class,
                        'options' => ['type' => \Zend\Filter\Boolean::TYPE_ALL],
                    ],
                ],
                'name'        => 'global',
                'description' => 'Mark the game as global',
            ],
            [
                'required'    => false,
                'allow_empty' => true,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\Boolean::class,
                        'options' => ['type' => \Zend\Filter\Boolean::TYPE_ALL],
                    ],
                ],
                'name'        => 'unity',
                'description' => 'Mark the game as a unity game',
            ],
            [
                'required'    => false,
                'allow_empty' => true,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\Boolean::class,
                        'options' => ['type' => \Zend\Filter\Boolean::TYPE_ALL],
                    ],
                ],
                'name'        => 'featured',
                'description' => 'Mark the game as featured',
            ],
            [
                'required'    => false,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Application\Filter\JsonToArrayFilter::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'meta',
                'description' => 'meta data for game',
            ],
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Game\Validator\UriValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'     => [
                    [
                        'name'    => \Application\Filter\JsonToArrayFilter::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'uris',
                'description' => 'URI\'s for the game',
            ],
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\Digits::class,
                        'options' => [],
                    ],
                ],
                'filters'     => [],
                'name'        => 'sort_order',
                'description' => 'Sort Order for the game',
            ],
        ],
        // TODO pull in the route params to body listener then add game_id and user_id to be validated
        'SaveGame\Validator'       => [
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Application\Filter\JsonToArrayFilter::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'data',
                'description' => 'The Data to save',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'version',
                'description' => 'The Version of the data',
            ],
        ],
    ],
];
