<?php

return [
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'service_manager' => [
        'initializers' => [
            \Notice\Factory\MailServiceAwareInitializer::class => \Notice\Factory\MailServiceAwareInitializer::class,
        ],
        'factories'    => [
            \Notice\NotifierListener::class         => \Notice\Factory\NotifierListenerFactory::class,
            \Notice\Listeners\ImportListener::class => \Notice\Factory\ImportListenerFactory::class,
            \Notice\Listeners\ForgotPasswordListener::class => \Notice\Factory\ForgotPasswordListenerFactory::class,
            \Notice\Listeners\NewUserEmailListener::class => \Notice\Factory\NewUserListenerFactory::class,
        ],
    ],

    'notify' => [
        'listeners' => [
            \Notice\Listeners\ImportListener::class,
            \Notice\Listeners\NewUserEmailListener::class,
            \Notice\Listeners\ForgotPasswordListener::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            'Notice\Controller' => \Notice\Controller\NoticeControllerFactory::class,
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'send-mail' => [
                    'options' => [
                        // @codingStandardsIgnoreStart
                        'route'    => 'sendmail --template= --email= [--verbose|-v] [--debug|-d]',
                        // @codingStandardsIgnoreEnd
                        'defaults' => [
                            'controller' => 'Notice\Controller',
                            'action'     => 'sendMail',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
