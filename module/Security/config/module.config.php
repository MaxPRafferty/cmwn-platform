<?php

return [
    'validators' => [
        'factories' => [
            \Security\PasswordValidator::class => \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class,
        ],
    ],

    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Security\Authentication\AuthAdapter::class => [
            \Security\Service\SecurityServiceInterface::class,
            \Zend\EventManager\EventManagerInterface::class,
        ],

        \Security\Listeners\RouteListener::class => [
            'Config',
            \Security\Service\SecurityOrgServiceInterface::class,
            \Security\Service\SecurityGroupServiceInterface::class,
            \Security\Service\SecurityUserServiceInterface::class,
        ],

        \Security\Guard\CsrfGuard::class => [
            'Config',
        ],

        \Security\Listeners\GroupServiceListener::class => [
            \Group\Service\UserGroupServiceInterface::class,
            \Security\Service\SecurityGroupServiceInterface::class,
        ],

        \Security\Listeners\OrgServiceListener::class => [
            \Security\Service\SecurityOrgServiceInterface::class,
            \Group\Service\UserGroupServiceInterface::class,
        ],

        \Security\PasswordValidator::class => [
            \Zend\Authentication\AuthenticationServiceInterface::class,
        ],

        \Security\Authorization\Assertion\UserAssertion::class => [
            \Security\Service\SecurityUserServiceInterface::class,
        ],

        \Security\Listeners\UserServiceListener::class => [
            \Group\Service\UserGroupServiceInterface::class,
        ],

        \Security\Guard\XsrfGuard::class => [
            'Config',
        ],

        \Security\Authorization\Rbac::class => [
            'Config',
        ],

        \Security\Listeners\HttpAuthListener::class => [
            \Zend\Authentication\Adapter\Http::class,
            \Security\Guard\CsrfGuard::class,
        ],

        \Security\Listeners\UserRouteListener::class => [
            \User\Service\UserServiceInterface::class,
            \Security\Authorization\Assertion\UserAssertion::class,
        ],

        \Security\Service\SecurityUserService::class => [
            'Table/UserGroups',
            \User\Service\UserServiceInterface::class,
        ],

        \Security\Service\SecurityOrgService::class => [
            \Zend\Db\Adapter\Adapter::class,
        ],

        \Security\Service\SecurityGroupService::class => [
            \Zend\Db\Adapter\Adapter::class,
        ],
    ],

    \Rule\Provider\Service\BuildProviderFromConfigFactory::class => [
        \Security\Rule\Provider\ActiveUserProvider::class => [
            \Security\Authentication\AuthenticationService::class,
        ],
        \Security\Rule\Provider\RoleProvider::class       => [
            \Security\Authentication\AuthenticationService::class,
        ],
        \Api\Rule\Provider\UserRelationshipProvider::class => [
            \Zend\Authentication\AuthenticationServiceInterface::class,
            \Security\Service\SecurityUserServiceInterface::class,
        ],
        \Api\Rule\Provider\ActiveUserGroupRoleProvider::class => [
            \Zend\Authentication\AuthenticationServiceInterface::class,
            \Security\Service\SecurityGroupServiceInterface::class,
        ],
        \Api\Rule\Provider\ActiveUserOrgRoleProvider::class => [
            \Zend\Authentication\AuthenticationServiceInterface::class,
            \Security\Service\SecurityOrgServiceInterface::class,
        ],
    ],

    'service_manager' => [
        'aliases' => [
            'authentication' =>
                \Security\Authentication\AuthenticationService::class,

            \Security\Service\SecurityUserServiceInterface::class     => \Security\Service\SecurityUserService::class,
            \Zend\Authentication\AuthenticationServiceInterface::class =>
                \Security\Authentication\AuthenticationService::class,
            \Security\Service\SecurityServiceInterface::class          => \Security\Service\SecurityService::class,
            \Zend\Authentication\AuthenticationService::class          =>
                \Security\Authentication\AuthenticationService::class,
            \Security\Service\SecurityOrgServiceInterface::class       => \Security\Service\SecurityOrgService::class,
            \Security\Service\SecurityGroupServiceInterface::class => \Security\Service\SecurityGroupService::class,
        ],

        'factories' => [
            \Security\Listeners\UpdateSession::class          => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Security\Listeners\UserUpdateListener::class     => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Security\Listeners\FetchUserImageListener::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Security\Service\SecurityService::class          => \Security\Service\SecurityServiceFactory::class,

            \Security\Authentication\AuthenticationService::class =>
                \Security\Authentication\AuthenticationServiceFactory::class,

            \Security\Listeners\ExpireAuthSessionListener::class =>
                \Security\Factory\ExpireAuthSessionListenerFactory::class,

            \Zend\Authentication\Adapter\Http::class =>
                \Security\Factory\BasicAuthAdapterFactory::class,

            \Zend\Authentication\Adapter\Http\ResolverInterface::class =>
                \Security\Factory\BasicAuthResolverFactory::class,

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
        \Security\Listeners\RouteListener::class,
        \Security\Guard\XsrfGuard::class,
        \Security\Guard\CsrfGuard::class,
        \Security\Listeners\ExpireAuthSessionListener::class,
        \Security\Listeners\UserServiceListener::class,
        \Security\Listeners\UpdateSession::class,
        \Security\Listeners\HttpAuthListener::class,
        \Security\Listeners\UserUpdateListener::class,
        \Security\Listeners\UserRouteListener::class,
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

    'providers' => [
        'factories' => [
            \Security\Rule\Provider\RoleProvider::class =>
                \Rule\Provider\Service\BuildProviderFromConfigFactory::class,

            \Security\Rule\Provider\ActiveUserProvider::class =>
                \Rule\Provider\Service\BuildProviderFromConfigFactory::class,
        ],

        'shared' => [
            \Api\Rule\Provider\UserRelationshipProvider::class => false,
            \Api\Rule\Provider\ActiveUserGroupRoleProvider::class  => false,
            \Api\Rule\Provider\ActiveUserOrgRoleProvider::class    => false,
        ],
    ],

    'rules' => [
        'factories' => [
            \Security\Rule\Rule\HasPermission::class => \Rule\Rule\Service\BuildRuleFactory::class,
            \Security\Rule\Rule\HasRole::class       => \Rule\Rule\Service\BuildRuleFactory::class,
        ],
        'shared'    => [
            \Security\Rule\Rule\HasPermission::class => false,
            \Security\Rule\Rule\HasRole::class       => false,
        ],
    ],
];
