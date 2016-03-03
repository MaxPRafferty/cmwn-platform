<?php

return [
    'service_manager' => [
        'invokables' => [
            'Security\Guard\CsrfGuard' => 'Security\Guard\CsrfGuard',
            'Security\Guard\OriginGuard' => 'Security\Guard\OriginGuard',
        ],
        'factories' => [
            'Security\Guard\ResetPasswordGuard' => 'Security\Guard\ResetPasswordGuardFactory',
            'Zend\Session\SessionManager'                       => 'Security\Session\SessionManagerFactory',
            'Security\Service\SecurityService'                  => 'Security\Service\SecurityServiceFactory',
            
            'Security\Authentication\CmwnAuthenticationAdapter'
                => 'Security\Authentication\CmwnAuthenticationAdapterFactory',
            'Security\Authentication\CmwnAuthenticationService'
                => 'Security\Authentication\CmwnAuthenticationServiceFactory',
        ]
    ],

    'controllers' => [
        'factories' => [
            'Security\Controller\User' => 'Security\Controller\UserControllerFactory',
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'add-user' => [
                    'options' => [
                        'route'    => 'create user',
                        'defaults' => [
                            'controller' => 'Security\Controller\User',
                            'action'     => 'createUser'
                        ],
                    ],
                ]
            ],
        ],
    ],
];
