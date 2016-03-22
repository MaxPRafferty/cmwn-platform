<?php

use \Security\Authorization\Rbac;

return [
    'service_manager' => [
        'aliases' => [
            'authentication'                            => 'Security\Authentication\AuthenticationService',
            'Security\Service\SecurityServiceInterface' => 'Security\Service\SecurityService',
        ],

        'invokables' => [
            'Security\Guard\XsrfGuard'              => 'Security\Guard\XsrfGuard',
            'Security\Guard\OriginGuard'            => 'Security\Guard\OriginGuard',
            'Security\Listeners\OrgServiceListener' => 'Security\Listeners\OrgServiceListener',
        ],

        'factories' => [
            'Security\Guard\ResetPasswordGuard'    => 'Security\Guard\ResetPasswordGuardFactory',
            'Security\Authorization\RouteListener' => 'Security\Authorization\RouteListenerFactory',
            'Zend\Session\SessionManager'          => 'Security\Session\SessionManagerFactory',
            'Security\Service\SecurityService'     => 'Security\Service\SecurityServiceFactory',
            'Security\Service\SecurityOrgService'  => 'Security\Service\SecurityOrgServiceFactory',
            'Security\Listeners\ListenersAggregate' => 'Security\Listeners\ListenersAggregateFactory',

            'Security\Authentication\AuthAdapter' => 'Security\Authentication\AuthAdapterFactory',
            'Security\Authorization\Rbac'         => 'Security\Authorization\RbacFactory',

            'Security\Authentication\AuthenticationService' =>
                'Security\Authentication\AuthenticationServiceFactory',
        ],

        'initializers' => [
            'Security\Authentication\AuthenticationServiceAwareInitializer' =>
                'Security\Authentication\AuthenticationServiceAwareInitializer',

            'Security\Authorization\RbacAwareInitializer' => 'Security\Authorization\RbacAwareInitializer'
        ],
    ],

    'controllers' => [
        'factories' => [
            'Security\Controller\User' => 'Security\Controller\UserControllerFactory',
        ],
    ],

    'security-listeners' => [
        'Security\Listeners\OrgServiceListener'
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

    'cmwn-roles' => [
        'super' => [
            'entity_bits' => [
                'group'        => Rbac::SCOPE_UPDATE | Rbac::SCOPE_CREATE | Rbac::SCOPE_REMOVE,
                'organization' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_CREATE | Rbac::SCOPE_REMOVE,
            ],
            'permissions' => [
                [
                    'permission' => 'view.all',
                    'label'      => 'View All Entities',
                ],
                [
                    'permission' => 'create.org',
                    'label'      => 'Create an organization',
                ],
                [
                    'permission' => 'edit.org',
                    'label'      => 'Edit an organization',
                ],
                [
                    'permission' => 'view.all.orgs',
                    'label'      => 'View an organization',
                ],
                [
                    'permission' => 'remove.org',
                    'label'      => 'Delete an organization',
                ],
                [
                    'permission' => 'remove.group',
                    'label'      => 'Delete a group',
                ],
                [
                    'permission' => 'create.user',
                    'label'      => 'Create a User',
                ],
                ['permission' => 'edit.user', 'label' => 'Edit a User'],
                ['permission' => 'remove.user', 'label' => 'Delete a User'],
            ],
        ],

        'admin' => [
            'parents'     => ['super'],
            'entity_bits' => [
                'group' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_CREATE,
            ],
            'permissions' => [
                [
                    'permission' => 'adult.code',
                    'label'      => 'Send code to adult',
                    'entity'     => 'adult',
                ],
                [
                    'permission' => 'create.group',
                    'label'      => 'Create a group',
                ],
                [
                    'permission' => 'remove.child.group',
                    'label'      => 'Remove a sub group',
                ],
                [
                    'permission' => 'create.child.group',
                    'label'      => 'Create a sub group',
                ],
                [
                    'permission' => 'import',
                    'label'      => 'Import file',
                ],
            ],
        ],

        'group_admin' => [
            'entity_bits' => [
                'group' => Rbac::SCOPE_UPDATE,
            ],
            'parents'     => ['admin'],
            'permissions' => [
                [
                    'permission' => 'edit.group',
                    'label'      => 'Edit a group',
                ],
                [
                    'permission' => 'child.code',
                    'label'      => 'Send code to child',
                ],
                [
                    'permission' => 'add.group.user',
                    'label'      => 'Add user to group',
                ],
                [
                    'permission' => 'remove.group.user',
                    'label'      => 'Remove user to group',
                ],
            ],
        ],

        'principal' => [
            'siblings' => ['group_admin', 'admin'],
        ],

        'asstprincipal' => [
            'siblings' => ['group_admin', 'admin'],
        ],

        'teacher' => [
            'siblings' => 'group_admin',
        ],

        'logged_in' => [
            'parents'     => ['group_admin'],
            'permissions' => [
                [
                    'permission' => 'read.group',
                    'label'      => 'Read group',
                ],
                [
                    'permission' => 'update.password',
                    'label'      => 'Update password',
                ],
                [
                    'permission' => 'view.games',
                    'label'      => 'View Games',
                ],
                [
                    'permission' => 'view.org',
                    'label'      => 'View an organization',
                ],
            ],
        ],
        'guest'     => [
            'parents' => ['group_admin'],
        ],
    ],
];
