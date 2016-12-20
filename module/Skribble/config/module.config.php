<?php

return [
    'validators' => [
        'invokables' => [
            \Skribble\Rule\RuleValidator::class => \Skribble\Rule\RuleValidator::class,
        ],
    ],

    'service_manager' => [
        'aliases'    => [
            \Skribble\Service\SkribbleServiceInterface::class => \Skribble\Service\SkribbleService::class,
        ],
        'factories'  => [
            \Skribble\Delegator\SkribbleServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Skribble\Service\SkribbleService::class => \Skribble\Service\SkribbleServiceFactory::class,
        ],
        'delegators' => [
            \Skribble\Service\SkribbleService::class => [
                \Skribble\Delegator\SkribbleServiceDelegatorFactory::class,
            ],
        ],
    ],
];
