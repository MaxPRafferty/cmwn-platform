<?php

return [
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'service_manager' => [
        'invokables'   => [
            \Notice\Listeners\ImportListener::class         => \Notice\Listeners\ImportListener::class,
            \Notice\Listeners\NewUserEmailListener::class   => \Notice\Listeners\NewUserEmailListener::class,
            \Notice\Listeners\ForgotPasswordListener::class => \Notice\Listeners\ForgotPasswordListener::class,
        ],
        'initializers' => [
            \Notice\Factory\MailServiceAwareInitializer::class => \Notice\Factory\MailServiceAwareInitializer::class,
        ],
        'factories'    => [
            \Notice\NotifierListener::class => \Notice\Factory\NotifierListenerFactory::class,
        ],
    ],

    'notify' => [
        'listeners' => [
            \Notice\Listeners\ImportListener::class,
            \Notice\Listeners\NewUserEmailListener::class,
            \Notice\Listeners\ForgotPasswordListener::class,
        ],
    ],
];
