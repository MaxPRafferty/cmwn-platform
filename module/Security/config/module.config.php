<?php

return [
    'service_manager' => [
        'aliases' => [
            'authentication' => 'Security\Authentication\AuthenticationService',
            'ZF\MvcAuth\Authentication' => 'Security\Authentication\AuthenticationService',
        ],
        'invokables' => [
            'Security\Guard\CsrfGuard' => 'Security\Guard\CsrfGuard',
            'Security\Guard\OriginGuard' => 'Security\Guard\OriginGuard',
            'Security\Authentication\AuthenticationDelegatorFactory'
                => 'Security\Authentication\AuthenticationDelegatorFactory'

        ],
        'factories' => [
            'Security\Guard\ResetPasswordGuard' => 'Security\Guard\ResetPasswordGuardFactory',
            'Zend\Session\SessionManager' => 'Security\Session\SessionManagerFactory',
            'Security\Service\SecurityService' => 'Security\Service\SecurityServiceFactory',

            'Security\Authentication\ApiAdapter' => 'Security\Authentication\ApiAdapterFactory',

            'Security\Authentication\AuthAdapter' => 'Security\Authentication\AuthAdapterFactory',

            'Security\Authentication\AuthenticationService'
                => 'Security\Authentication\AuthenticationServiceFactory',
        ],
        'delegators' => [
            'ZF\MvcAuth\Authentication\DefaultAuthenticationListener' => [
                'Security\Authentication\AuthenticationDelegatorFactory'
            ]
        ],
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
                ],
            ],
        ],
    ],

    'zf-mvc-auth' => [
        'authentication' => [
            'types' => [
                'api',
            ],
            'map' => [
                'Login' => 'user'
            ],
            'adapters' => [
                'user' => [
                    // This defines an OAuth2 adapter backed by PDO.
                    'adapter' => 'Security\Authentication\ApiAdapter',
                    'storage' => [
                        'adapter' => 'pdo',
                        'dsn' => 'mysql:dbname=cmwn;host=localhost;dbname=cmwn',
                        'username' => 'root',
                        'password' => 'root123$',
                        'options' => [
                            1002 => 'SET NAMES utf8', // PDO::MYSQL_ATTR_INIT_COMMAND
                        ],
                    ],
                ],
            ],
        ],
        'authorization' => [
            'deny_by_default' => false,
            'Api\V1\Rest\Login\Controller' => [
                'default' => true
            ]
        ],
    ],
];
