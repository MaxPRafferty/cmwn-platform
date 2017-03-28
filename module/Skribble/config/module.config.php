<?php

return [
    'validators' => [
        'invokables' => [
            \Skribble\Rule\RuleValidator::class => \Skribble\Rule\RuleValidator::class,
        ],
    ],

    'rules' => [
        'factories' => [
            \Skribble\Rule\Rule\SkribbleStatusEqualsRule::class => \Rule\Rule\Service\BuildRuleFactory::class,
        ],
        'shared'    => [
            \Skribble\Rule\Rule\SkribbleStatusEqualsRule::class => false,
        ],
    ],

    'service_manager' => [
        'aliases'    => [
            \Skribble\Service\SkribbleServiceInterface::class => \Skribble\Service\SkribbleService::class,
        ],
        'factories'  => [
//            \Skribble\Delegator\SkribbleServiceDelegatorFactory::class =>
//                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Skribble\Service\SkribbleService::class                   =>
                \Skribble\Service\SkribbleServiceFactory::class,
        ],
        'delegators' => [
            \Skribble\Service\SkribbleService::class => [
                \Skribble\Delegator\SkribbleServiceDelegatorFactory::class,
            ],
        ],
    ],
];
