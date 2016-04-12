<?php

return [
    'service_manager' => [
        'aliases' => [
            'authentication' => 'Security\Authentication\AuthenticationService',
        ],

        'invokables' => [
            \Security\Guard\OriginGuard::class              => \Security\Guard\OriginGuard::class,
            \Security\Listeners\OrgServiceListener::class   => \Security\Listeners\OrgServiceListener::class,
            \Security\Listeners\GroupServiceListener::class => \Security\Listeners\GroupServiceListener::class,
        ],

        'factories' => [
            \Security\Guard\CsrfGuard::class             => \Security\Factory\CsrfGuardFactory::class,
            \Security\Guard\XsrfGuard::class             => \Security\Factory\XsrfGuardFactory::class,
            \Security\Authorization\RouteListener::class => \Security\Authorization\RouteListenerFactory::class,
            \Zend\Session\SessionManager::class          => \Security\Session\SessionManagerFactory::class,
            \Security\Service\SecurityService::class     => \Security\Service\SecurityServiceFactory::class,
            \Security\Service\SecurityOrgService::class  => \Security\Service\SecurityOrgServiceFactory::class,
            \Security\Authentication\AuthAdapter::class  => \Security\Authentication\AuthAdapterFactory::class,
            \Security\Authorization\Rbac::class          => \Security\Authorization\RbacFactory::class,

            'Security\Authentication\AuthenticationService' =>
                \Security\Authentication\AuthenticationServiceFactory::class,

            \Security\Listeners\ExpireAuthSessionListener::class =>
                \Security\Factory\ExpireAuthSessionListenerFactory::class,
        ],

        'initializers' => [
            \Security\Authentication\AuthenticationServiceAwareInitializer::class =>
                \Security\Authentication\AuthenticationServiceAwareInitializer::class,

            \Security\Authorization\RbacAwareInitializer::class => \Security\Authorization\RbacAwareInitializer::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            'Security\Controller\User' => 'Security\Controller\UserControllerFactory',
        ],
    ],

    'shared-listeners' => [
        \Security\Listeners\OrgServiceListener::class,
        \Security\Listeners\GroupServiceListener::class,
        \Security\Authorization\RouteListener::class,
        \Security\Guard\OriginGuard::class,
        \Security\Guard\XsrfGuard::class,
        \Security\Guard\CsrfGuard::class,
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
