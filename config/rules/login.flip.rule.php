<?php
/**
 * Earn a flip at login
 */
return [
    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        'first-time-login' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'first-time-login',
            'name'                => 'Awards a flip to a user when they login for the first time',
            'when'                => 'login.success',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Cannot have earned the flip before
                    [
                        'rule_class' => \Flip\Rule\Rule\EarnedFlip::class,
                        'options'    => [
                            \Flip\Service\FlipUserServiceInterface::class,
                            'first-time-login',
                            'active_user',
                        ],
                        'operator'   => 'not',
                    ],

                    // Has permission to earn flips
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'earn.flip',
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    [
                        'action_class' => \Flip\Rule\Action\EarnFlip::class,
                        \Flip\Service\FlipUserServiceInterface::class,
                        'first-time-login',
                        'active_user',
                    ],
                ],
            ],
            'providers'           => [
                'foo' => 'bar',
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'first-time-login' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
