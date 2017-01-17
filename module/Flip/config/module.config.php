<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Flip\Service\FlipUserService::class => ['Table/UserFlips'],
        \Flip\Service\FlipService::class     => ['FlipsTable'],
    ],

    'actions' => [
        'factories' => [
            \Flip\Rule\Action\EarnFlip::class              => \Rule\Action\Service\BuildActionFactory::class,
            \Flip\Rule\Action\AddAcknowledgeHeaders::class => \Rule\Action\Service\BuildActionFactory::class,
        ],
        'shared'    => [
            \Flip\Rule\Action\EarnFlip::class              => false,
            \Flip\Rule\Action\AddAcknowledgeHeaders::class => false,
        ],
    ],

    'rules' => [
        'factories' => [
            \Flip\Rule\Rule\EarnedFlip::class         => \Rule\Rule\Service\BuildRuleFactory::class,
            \Flip\Rule\Rule\EarnedFlipXTimes::class   => \Rule\Rule\Service\BuildRuleFactory::class,
            \Flip\Rule\Rule\FlipRegistered::class     => \Rule\Rule\Service\BuildRuleFactory::class,
            \Flip\Rule\Rule\HasAcknowledgeFlip::class => \Rule\Rule\Service\BuildRuleFactory::class,
        ],
        'shared'    => [
            \Flip\Rule\Rule\EarnedFlip::class         => false,
            \Flip\Rule\Rule\FlipRegistered::class     => false,
            \Flip\Rule\Rule\EarnedFlipXTimes::class   => false,
            \Flip\Rule\Rule\HasAcknowledgeFlip::class => false,
        ],
    ],

    'providers' => [
        'factories' => [
            \Flip\Rule\Provider\AcknowledgeFlip::class => \Rule\Provider\Service\BuildProviderFactory::class,
        ],
        'shared'    => [
            \Flip\Rule\Provider\AcknowledgeFlip::class => false,
        ],
    ],

    'service_manager' => [
        'aliases'    => [
            \Flip\Service\FlipServiceInterface::class     => \Flip\Service\FlipService::class,
            \Flip\Service\FlipUserServiceInterface::class => \Flip\Service\FlipUserService::class,
        ],
        'factories'  => [
            \Flip\Delegator\FlipServiceDelegatorFactory::class     =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Flip\Delegator\FlipUserServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'delegators' => [
            \Flip\Service\FlipService::class     => [
                \Flip\Delegator\FlipServiceDelegatorFactory::class,
            ],
            \Flip\Service\FlipUserService::class => [
                \Flip\Delegator\FlipUserServiceDelegatorFactory::class,
            ],
        ],
    ],
];
