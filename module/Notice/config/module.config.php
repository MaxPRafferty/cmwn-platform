<?php

return [
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'Notice\Listeners\ImportListener' => 'Notice\Listeners\ImportListener',
            'Notice\Listeners\NewUserEmailListener' => 'Notice\Listeners\NewUserEmailListener',
        ],
        'initializers' => [
            'Notice\Factory\MailServiceAwareInitializer' => 'Notice\Factory\MailServiceAwareInitializer',
        ],
        'factories' => [
            'Notice\NotifierListener' => 'Notice\Factory\NotifierListenerFactory',
        ],
    ],

    'notify' => [
        'listeners' => [
            'Notice\Listeners\ImportListener',
            'Notice\Listeners\NewUserEmailListener'
        ],
    ],
];
