<?php
use \Security\Authorization\Rbac;

return [

    'cmwn-roles' => [
        'permission_labels' => [
            'add.group.user'     => 'Add user to group',
            'adult.code'         => 'Send adult reset code',
            'child.code'         => 'Send child reset code',
            'create.child.group' => 'Create a sub group',
            'create.group'       => 'Create a group',
            'create.org'         => 'Create an Organization',
            'create.user'        => 'Create a user',
            'edit.group'         => 'Edit a Group',
            'edit.org'           => 'Edit an Organization',
            'edit.user'          => 'Edit a User',
            'import'             => 'Import Data file',
            'read.group'         => 'View Group',
            'remove.child.group' => 'Remove a child group',
            'remove.group'       => 'Remove a group',
            'remove.group.user'  => 'Remove user from group',
            'remove.org'         => 'Remove an Organization',
            'remove.user'        => 'Delete a user',
            'update.password'    => 'Update profile password',
            'view.all.groups'    => 'View all Groups',
            'view.all.orgs'      => 'View all Organizations',
            'view.games'         => 'View all Games',
            'view.group.users'   => 'View Group users',
            'view.org'           => 'View an Organization',
            'view.org.users'     => 'View all users in an organization',
        ],
        'roles'             => [
            'super' => [
                'entity_bits' => [
                    'group'        => -1,
                    'organization' => -1,
                    'user'         => -1,
                    'me'           => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.groups',
                    'view.all.orgs',
                ],
            ],

            'admin' => [
                'parents'     => ['super'],
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_CREATE,
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'adult.code',
                    'create.child.group',
                    'create.group',
                    'import',
                    'remove.child.group',
                    'view.org.users',
                ],
            ],

            'group_admin' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE,
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'parents'     => ['admin'],
                'permissions' => [
                    'add.group.user',
                    'child.code',
                    'edit.group',
                    'read.group',
                    'remove.group.user',
                    'view.group.users',
                    'view.org.users',
                ],
            ],

            'principal' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE,
                    'user'  => Rbac::SCOPE_UPDATE,
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'import',
                    'read.group',
                    'remove.child.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                ],
            ],

            'asst_principal' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE,
                    'user'  => Rbac::SCOPE_UPDATE,
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'import',
                    'read.group',
                    'remove.child.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                ],
            ],

            'teacher' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE,
                    'user'  => Rbac::SCOPE_UPDATE,
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'edit.group',
                    'read.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                ],
            ],

            'logged_in' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'parents'     => ['group_admin'],
                'permissions' => [
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                ],
            ],

            'guest' => [
                'parents' => ['logged_in'],
            ],
        ],
    ],
];