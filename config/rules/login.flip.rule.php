<?php
$flipToEarn = 'first-time-login';
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
                    // Has permission to earn flips
                    [
                        'name'    => \Security\Rule\Rule\HasPermission::class,
                        'options' => [
                            \Security\Authorization\Rbac::class,
                            'create.user.flip',
                        ],
                    ],

                    // The flip is registered
                    [
                        'name'    => \Flip\Rule\Rule\FlipRegistered::class,
                        'options' => [
                            \Flip\Service\FlipServiceInterface::class,
                            $flipToEarn,
                        ],
                    ],

                    // Cannot have earned the flip before
                    [
                        'name'     => \Flip\Rule\Rule\EarnedFlip::class,
                        'options'  => [
                            \Flip\Service\FlipUserServiceInterface::class,
                            $flipToEarn,
                            'active_user',
                        ],
                        'operator' => 'not',
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    // Earn the flip for the active user
                    [
                        'name'    => \Flip\Rule\Action\EarnFlip::class,
                        'options' => [
                            \Flip\Service\FlipUserServiceInterface::class,
                            $flipToEarn,
                            'active_user',
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\ActiveUserProvider::class,
                \Security\Rule\Provider\RoleProvider::class,
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'first-time-login' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
