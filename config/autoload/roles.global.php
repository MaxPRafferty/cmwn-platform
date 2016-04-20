<?php
use \Security\Authorization\Rbac;

return [
    'cmwn-roles' => [
        'permission_labels' => [

            // user
            'create.user'        => 'Create a user',
            'view.user.adult'    => 'View an Adults Information',
            'view.user.child'    => 'View a child\'s Information',
            'edit.user.adult'    => 'Edit an Adult',
            'edit.user.child'    => 'Edit a Child',
            'pick.username'      => 'Pick a new User Name',
            'update.password'    => 'Update profile password',
            'remove.user.adult'  => 'Delete a child user',
            'remove.user.child'  => 'Delete an adult user',

            // group
            'create.child.group' => 'Create a sub group',
            'create.group'       => 'Create a group',
            'view.group'         => 'View Group',
            'view.all.groups'    => 'View all Groups',
            'edit.group'         => 'Edit a Group',
            'import'             => 'Import Data file',
            'remove.child.group' => 'Remove a child group',
            'remove.group'       => 'Remove a group',

            // user group
            'add.group.user'     => 'Add user to group',
            'remove.group.user'  => 'Remove user from group',
            'view.group.users'   => 'View Group users',

            // organizations
            'create.org'         => 'Create an Organization',
            'view.all.orgs'      => 'View all Organizations',
            'view.org'           => 'View an Organization',
            'view.org.users'     => 'View all users in an organization',
            'edit.org'           => 'Edit an Organization',
            'remove.org'         => 'Remove an Organization',

            // game
            'view.games'         => 'View all Games',

            // misc
            'adult.code'         => 'Send adult reset code',
            'child.code'         => 'Send child reset code',
        ],
        'roles'             => [
            'super' => [
                'entity_bits' => [
                    'group'        => -1,
                    'organization' => -1,
                    'user'         => -1,
                    'me'           => -1,
                ],
                'permissions' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user.adult',
                    'edit.user.child',
                    'remove.group',
                    'remove.org',
                    'remove.user.adult',
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
                    'edit.user.child',
                    'remove.group.user',
                    'remove.user.child',
                    'view.group',
                    'view.group.users',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                ],
            ],

            'principal' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'user'  => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
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
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
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
                    'child.code',
                    'edit.group',
                    'edit.user.child',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.user.adult',
                    'view.user.child',
                ],
            ],

            'neighbor.adult' => [
                'entity_bits' => [],
                'permissions' => [
                    'view.user.adult',
                ],
            ],

            'me' => [
                'entity_bits' => [
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'edit.user.adult',
                    'edit.user.child',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.user.adult',
                    'view.user.child',
                ],
            ],

            'child' => [
                'entity_bits' => [
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'child.code',
                    'pick.username',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.user.adult',
                    'view.user.child',
                ],
            ],

            'student' => [
                'entity_bits' => [
                    'me'    => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'child.code',
                    'pick.username',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.user.adult',
                    'view.user.child',
                ],
            ],

            'logged_in' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                ],
                'parents'     => ['group_admin'],
                'permissions' => [
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.org',
                ],
            ],

            'guest' => [
                'parents' => ['logged_in'],
            ],
        ],
    ],
];