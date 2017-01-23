<?php
return [

    \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class => [
        /**
         * This is a rule that check at login if the user has a flip that needs to be acknowledged
         *
         * The flip that needs to be acknowledged will be stored in the session
         */
        'store-ack-flip'      => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'store-ack-flip',
            'name'                => 'Stores a flip that needs to be acknowledged in the session',
            'when'                => 'login.success',
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Checks if there is a flip that needs to be acknowledged
                    [
                        'name'    => \Flip\Rule\Rule\HasAcknowledgeFlip::class,
                        'options' => [
                            \Flip\Service\FlipUserServiceInterface::class,
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    // Write the acknowledge flip to the container
                    [
                        'name'    => \Application\Rule\Session\Action\WriteProviderToSession::class,
                        'options' => [
                            \Application\Session\CmwnContainer::class,
                            \Flip\Rule\Provider\AcknowledgeFlip::PROVIDER_NAME,
                        ],
                    ],
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\ActiveUserProvider::class,
            ],
        ],

        /**
         * This is a rule that will check the session at the the finish event
         *
         * It will then append the headers to the response
         */
        'add-ack-flip-header' => [
            'specification_class' => \Rule\Engine\Specification\EngineSpecification::class,
            'id'                  => 'add-ack-flip-header',
            'name'                => 'Appends the headers needed to acknowledge a flip',
            'when'                => \Zend\Mvc\MvcEvent::EVENT_FINISH,
            'rules'               => [
                'rule_collection_class' => \Rule\Rule\Collection\RuleCollection::class,
                'rules'                 => [
                    // Checks if there is a flip that needs to be acknowledged
                    [
                        'name'    => \Flip\Rule\Rule\HasAcknowledgeFlip::class,
                        'options' => [
                            \Flip\Service\FlipUserServiceInterface::class,
                        ],
                    ],
                ],
            ],
            'actions'             => [
                'action_collection_class' => \Rule\Action\Collection\ActionCollection::class,
                'actions'                 => [
                    // Write the acknowledge flip to the container
                    \Flip\Rule\Action\AddAcknowledgeHeaders::class,
                ],
            ],
            'providers'           => [
                \Security\Rule\Provider\ActiveUserProvider::class,
                \Rule\Event\Provider\EventProvider::class,
            ],
        ],
    ],

    'specifications' => [
        'factories' => [
            'store-ack-flip' => \Rule\Engine\Service\BuildSpecificationFromConfigFactory::class,
        ],
    ],
];
