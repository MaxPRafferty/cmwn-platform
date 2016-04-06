<?php

use \Security\Authorization\Rbac;

return [
    'service_manager' => [
        'aliases' => [
            'authentication'                            => 'Security\Authentication\AuthenticationService',
            'Security\Service\SecurityServiceInterface' => 'Security\Service\SecurityService',
        ],

        'invokables' => [
            'Security\Guard\CsrfGuard'                => 'Security\Guard\CsrfGuard',
            'Security\Guard\XsrfGuard'                => 'Security\Guard\XsrfGuard',
            'Security\Guard\OriginGuard'              => 'Security\Guard\OriginGuard',
            'Security\Listeners\OrgServiceListener'   => 'Security\Listeners\OrgServiceListener',
            'Security\Listeners\GroupServiceListener' => 'Security\Listeners\GroupServiceListener',
        ],

        'factories' => [
            'Security\Guard\ResetPasswordGuard'    => 'Security\Guard\ResetPasswordGuardFactory',
            'Security\Authorization\RouteListener' => 'Security\Authorization\RouteListenerFactory',
            'Zend\Session\SessionManager'          => 'Security\Session\SessionManagerFactory',
            'Security\Service\SecurityService'     => 'Security\Service\SecurityServiceFactory',
            'Security\Service\SecurityOrgService'  => 'Security\Service\SecurityOrgServiceFactory',

            'Security\Authentication\AuthAdapter' => 'Security\Authentication\AuthAdapterFactory',
            'Security\Authorization\Rbac'         => 'Security\Authorization\RbacFactory',

            'Security\Authentication\AuthenticationService' =>
                'Security\Authentication\AuthenticationServiceFactory',

            \Security\Listeners\ExpireAuthSessionListener::class =>
                \Security\Factory\ExpireAuthSessionListenerFactory::class,
        ],

        'initializers' => [
            'Security\Authentication\AuthenticationServiceAwareInitializer' =>
                'Security\Authentication\AuthenticationServiceAwareInitializer',

            'Security\Authorization\RbacAwareInitializer' => 'Security\Authorization\RbacAwareInitializer',
        ],
    ],

    'controllers' => [
        'factories' => [
            'Security\Controller\User' => 'Security\Controller\UserControllerFactory',
        ],
    ],

    'shared-listeners' => [
        'Security\Listeners\OrgServiceListener',
        'Security\Listeners\GroupServiceListener',
        'Security\Authorization\RouteListener',
        'Security\Guard\OriginGuard',
        'Security\Guard\XsrfGuard',
        'Security\Guard\ResetPasswordGuard',
        'Security\Guard\CsrfGuard',
        \Security\Listeners\ExpireAuthSessionListener::class,
    ],

    'console' => [
        'router' => [
            'routes' => [
                'add-user' => [
                    'options' => [
                        'route'    => 'create user',
                        'defaults' => [
                            'controller' => 'Security\Controller\User',
                            'action'     => 'createUser',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
