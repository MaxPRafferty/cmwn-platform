<?php

return [
    'rules' => [
        'aliases'   => [
            \Rule\Rule\Collection\RuleCollectionInterface::class => \Rule\Rule\Collection\RuleCollection::class,
        ],
        'services'  => [
            \Rule\Rule\Basic\AlwaysSatisfiedRule::class => new \Rule\Rule\Basic\AlwaysSatisfiedRule(),
            \Rule\Rule\Basic\NeverSatisfiedRule::class  => new \Rule\Rule\Basic\NeverSatisfiedRule(),
            \Rule\Rule\Collection\RuleCollection::class => new \Rule\Rule\Collection\RuleCollection(),
        ],
        'factories' => [
            \Rule\Rule\Basic\AlwaysSatisfiedRule::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Rule\Rule\Basic\NeverSatisfiedRule::class  => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Rule\Rule\Basic\AndRule::class             => \Rule\Rule\Service\DependantRuleFactory::class,
            \Rule\Rule\Basic\NotRule::class             => \Rule\Rule\Service\DependantRuleFactory::class,
            \Rule\Rule\Basic\EitherRule::class          => \Rule\Rule\Service\DependantRuleFactory::class,
            \Rule\Rule\Collection\RuleCollection::class => \Rule\Rule\Service\RuleCollectionFactory::class,
        ],
        'shared'    => [
            \Rule\Rule\Basic\AlwaysSatisfiedRule::class => true,
            \Rule\Rule\Basic\NeverSatisfiedRule::class  => true,
            \Rule\Rule\Collection\RuleCollection::class => false,
            \Rule\Rule\Basic\AndRule::class             => false,
            \Rule\Rule\Basic\NotRule::class             => false,
            \Rule\Rule\Basic\EitherRule::class          => false,
        ],
    ],

    'providers' => [],

    'actions' => [],

    'specifications' => [],

    'service_manager' => [
        'aliases'   => [
            'config' => 'Config',
        ],
        'factories' => [
            \Rule\Rule\Service\RuleManager::class => \Rule\Rule\Service\RuleManagerFactory::class,
        ],
    ],
];
