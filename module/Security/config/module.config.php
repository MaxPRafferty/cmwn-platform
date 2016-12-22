<?php

return [
    'validators' => [
        'factories' => [
            \Security\PasswordValidator::class => \Security\Factory\PasswordValidatorFactory::class,
        ],
    ],

    'service_manager' => [
        'aliases' => [
            'authentication' =>
                \Security\Authentication\AuthenticationService::class,

            \Security\Service\SecurityGroupServiceInterface::class     => \Security\Service\SecurityGroupService::class,
            \Zend\Authentication\AuthenticationServiceInterface::class =>
                \Security\Authentication\AuthenticationService::class,
            \Security\Service\SecurityServiceInterface::class          => \Security\Service\SecurityService::class,
            \Zend\Authentication\AuthenticationService::class          =>
                \Security\Authentication\AuthenticationService::class,
            \Security\Service\SecurityOrgServiceInterface::class       => \Security\Service\SecurityOrgService::class,
        ],

        'factories' => [
            \Security\Listeners\UpdateSession::class          => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Security\Listeners\UserUpdateListener::class     => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Security\Listeners\FetchUserImageListener::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Security\Guard\CsrfGuard::class                  => \Security\Factory\CsrfGuardFactory::class,
            \Security\Guard\XsrfGuard::class                  => \Security\Factory\XsrfGuardFactory::class,
            \Security\Authorization\RouteListener::class      => \Security\Authorization\RouteListenerFactory::class,
            \Security\Service\SecurityService::class          => \Security\Service\SecurityServiceFactory::class,
            \Security\Service\SecurityOrgService::class       => \Security\Service\SecurityOrgServiceFactory::class,
            \Security\Authentication\AuthAdapter::class       => \Security\Authentication\AuthAdapterFactory::class,
            \Security\Authorization\Rbac::class               => \Security\Authorization\RbacFactory::class,

            \Security\Listeners\UserServiceListener::class => \Security\Factory\UserServiceListenerFactory::class,

            \Security\Service\SecurityGroupService::class =>
                \Security\Service\SecurityGroupServiceFactory::class,

            \Security\Authentication\AuthenticationService::class =>
                \Security\Authentication\AuthenticationServiceFactory::class,

            \Security\Listeners\ExpireAuthSessionListener::class =>
                \Security\Factory\ExpireAuthSessionListenerFactory::class,

            \Security\Authorization\Assertions\UserAssertion::class =>
                \Security\Factory\UserAssertionFactory::class,

            \Security\Listeners\GroupServiceListener::class =>
                \Security\Factory\GroupServiceListenerFactory::class,
            \Security\Listeners\OrgServiceListener::class   =>
                \Security\Factory\OrgServiceListenerFactory::class,

            \Zend\Authentication\Adapter\Http::class =>
                \Security\Factory\BasicAuthAdapterFactory::class,

            \Zend\Authentication\Adapter\Http\ResolverInterface::class =>
                \Security\Factory\BasicAuthResolverFactory::class,

            \Security\Listeners\HttpAuthListener::class   => \Security\Factory\HttpAuthListenerFactory::class,
            \Security\Utils\PermissionTableFactory::class => \Security\Utils\PermissionTableBuilderFactory::class,
        ],

        'initializers' => [
            \Security\Authentication\AuthenticationServiceAwareInitializer::class =>
                \Security\Authentication\AuthenticationServiceAwareInitializer::class,

            \Security\Authorization\RbacAwareInitializer::class => \Security\Authorization\RbacAwareInitializer::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            'Security\Controller\User' => \Security\Controller\UserControllerFactory::class,
            'Security\Controller\Perm' => \Security\Controller\PermControllerFactory::class,
        ],
    ],

    'shared-listeners' => [
        \Security\Listeners\OrgServiceListener::class,
        \Security\Listeners\GroupServiceListener::class,
        \Security\Authorization\RouteListener::class,
        \Security\Guard\XsrfGuard::class,
        \Security\Guard\CsrfGuard::class,
        \Security\Listeners\ExpireAuthSessionListener::class,
        \Security\Listeners\UserServiceListener::class,
        \Security\Listeners\UpdateSession::class,
        \Security\Listeners\HttpAuthListener::class,
        \Security\Listeners\UserUpdateListener::class,
        \Security\Listeners\FetchUserImageListener::class,
    ],

    'console' => [
        'router' => [
            'routes' => [
                'add-user' => [
                    'options' => [
                        'route'    => 'create:user',
                        'defaults' => [
                            'controller' => 'Security\Controller\User',
                            'action'     => 'createUser',
                        ],
                    ],
                ],

                'permissions' => [
                    'options' => [
                        'route'    => 'security:perms [--csv=] [--roles=]',
                        'defaults' => [
                            'controller' => 'Security\Controller\Perm',
                            'action'     => 'showPerm',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
