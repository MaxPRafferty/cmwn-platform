<?php

return [
    'job_runner' => [
        'allowed_jobs' => [
            'Suggest\Engine\SuggestionEngine' => [
                'command' => 'suggest',
                'params'  => [
                    'user_id',
                ],
            ],
        ],
    ],

    'service_manager' => [
        'aliases'    => [
            \Suggest\Service\SuggestedServiceInterface::class => \Suggest\Service\SuggestedService::class,
        ],
        'invokables' => [
            \Suggest\Delegator\SuggestedServiceDelegatorFactory::class =>
                \Suggest\Delegator\SuggestedServiceDelegatorFactory::class,
            \Suggest\Rule\TypeRule::class => \Suggest\Rule\TypeRule::class,
            \Suggest\Rule\MeRule::class => \Suggest\Rule\MeRule::class,
        ],
        'factories'  => [
            \Suggest\Service\SuggestedService::class  => \Suggest\Service\SuggestedServiceFactory::class,
            \Suggest\Engine\SuggestionEngine::class => \Suggest\Engine\SuggestionEngineFactory::class,
            \Suggest\Filter\ClassFilter::class => \Suggest\Filter\ClassFilterFactory::class,
            \Suggest\Rule\FriendRule::class => \Suggest\Rule\FriendRuleFactory::class,
            \Suggest\Listener\TriggerSuggestionsListener::class =>
                \Suggest\Listener\TriggerSuggestionsListenerFactory::class,
            \Suggest\Listener\DeleteSuggestionListener::class =>
                \Suggest\Listener\DeleteSuggestionListenerFactory::class
        ],
        'delegators' => [
            \Suggest\Service\SuggestedService::class          => [
                \Suggest\Delegator\SuggestedServiceDelegatorFactory::class,
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            'Suggest\Controller\SuggestionController' => \Suggest\Controller\SuggestionControllerFactory::class,
            'Suggest\Controller\SuggestCronController' => \Suggest\Controller\SuggestCronControllerFactory::class,
        ],
    ],

    'shared-listeners' => [
        \Suggest\Listener\TriggerSuggestionsListener::class,
        \Suggest\Listener\DeleteSuggestionListener::class,
    ],

    'console' => [
        'router' => [
            'routes' => [
                'suggest-cli' => [
                    'options' => [
                        // @codingStandardsIgnoreStart
                        'route'    => 'suggest --user_id= [--verbose|-v] [--debug|-d]',
                        // @codingStandardsIgnoreEnd
                        'defaults' => [
                            'controller' => 'Suggest\Controller\SuggestionController',
                            'action'     => 'suggest',
                        ],
                    ],
                ],

                'suggest-cron' => [
                    'options' => [
                        'route'    => 'cron:suggest [--verbose|-v] [--debug|-d]',
                        'defaults' => [
                            'controller' => 'Suggest\Controller\SuggestCronController',
                            'action'     => 'suggestCron',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
