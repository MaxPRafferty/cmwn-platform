<?php
use \Security\Authorization\Rbac;

return [
    'cmwn-roles' => [
        'permission_labels' => [
            // super
            'view.all.users'       => 'View all users',

            // user
            'create.user'          => 'Create a user',
            'view.user.adult'      => 'View an Adults Information',
            'view.user.child'      => 'View a child\'s Information',
            'edit.user.adult'      => 'Edit an Adult',
            'edit.user.child'      => 'Edit a Child',
            'pick.username'        => 'Pick a new User Name',
            'update.password'      => 'Update profile password',
            'remove.user.adult'    => 'Delete a child user',
            'remove.user.child'    => 'Delete an adult user',
            'can.friend'           => 'Can friend users',

            // Flip
            'create.user.flip'     => 'Earn a flip',
            'view.flip'            => 'View flip information',
            'view.user.flip'       => 'View Flips for a user',

            // group
            'create.child.group'   => 'Create a sub group',
            'create.group'         => 'Create a group',
            'view.group'           => 'View Group',
            'view.user.groups'     => 'View Groups of a user',
            'view.all.groups'      => 'View all Groups',
            'edit.group'           => 'Edit a Group',
            'import'               => 'Import Data file',
            'remove.child.group'   => 'Remove a child group',
            'remove.group'         => 'Remove a group',

            // user group
            'add.group.user'       => 'Add user to group',
            'remove.group.user'    => 'Remove user from group',
            'view.group.users'     => 'View Group users',

            // organizations
            'create.org'           => 'Create an Organization',
            'view.all.orgs'        => 'View all Organizations',
            'view.org'             => 'View an Organization',
            'view.user.orgs'       => 'View all Organizations the user belongs too',
            'view.org.users'       => 'View all users in an organization',
            'edit.org'             => 'Edit an Organization',
            'remove.org'           => 'Remove an Organization',

            // game
            'view.games'           => 'View all Games',
            'save.game'            => 'Save Game progress',

            // misc
            'adult.code'           => 'Send adult reset code',
            'child.code'           => 'Send child reset code',
            'attach.profile.image' => 'Upload a profile image',
            'view.profile.image'   => 'View a users profile image',

            // skribble
            'view.skribble'        => 'Read Skribbles',
            'create.skribble'      => 'Create Skribbles',
            'delete.skribble'      => 'Delete Skribbles',
            'update.skribble'      => 'Update Skribbles',
            'skribble.notice'      => 'Notify Skribble status',
        ],
        'roles'             => [
            'super' => [
                'entity_bits' => [
                    'group'        => -1,
                    'organization' => -1,
                    'adult'        => -1,
                    'child'        => -1,
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
                    'view.all.users',
                    'view.flip',
                    'view.user.flip',
                    'view.profile.image',
                    'adult.code',
                    'child.code',
                    'skribble.notice',
                ],
            ],

            'admin' => [
                'parents'     => ['super'],
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_CREATE,
                    'me'    => Rbac::SCOPE_UPDATE,
                    'child' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'adult' => Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'adult.code',
                    'create.child.group',
                    'create.group',
                    'import',
                    'remove.child.group',
                    'view.org.users',
                    'view.profile.image',
                    'remove.user.adult',
                ],
            ],

            'group_admin' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE,
                    'me'    => Rbac::SCOPE_UPDATE,
                    'child' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'adult' => Rbac::SCOPE_REMOVE,
                ],
                'parents'     => ['admin'],
                'permissions' => [
                    'add.group.user',
                    'child.code',
                    'edit.group',
                    'edit.user.child',
                    'remove.group.user',
                    'remove.user.child',
                    'remove.user.adult',
                    'view.group',
                    'view.group.users',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                    'view.profile.image',
                ],
            ],

            'principal.adult' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'adult' => Rbac::SCOPE_REMOVE,
                    'child' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'me'    => Rbac::SCOPE_UPDATE,
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
                    'remove.user.adult',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.user.groups',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                    'view.profile.image',
                ],
            ],

            'asst_principal.adult' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE,
                    'adult' => Rbac::SCOPE_REMOVE,
                    'child' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'me'    => Rbac::SCOPE_UPDATE,
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
                    'view.profile.image',
                    'remove.user.adult',
                ],
            ],

            'teacher.adult' => [
                'entity_bits' => [
                    'group' => Rbac::SCOPE_UPDATE,
                    'child' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'me'    => Rbac::SCOPE_UPDATE,
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
                    'view.org.users',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.groups',
                ],
            ],

            'neighbor.adult.adult' => [
                'entity_bits' => [
                    'adult' => Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'adult.code',
                    'remove.user.adult',
                    'view.user.adult',
                    'view.profile.image',
                ],
            ],

            'me.child' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'attach.profile.image',
                    'can.friend',
                    'create.skribble',
                    'create.user.flip',
                    'delete.skribble',
                    'edit.user.adult',
                    'edit.user.child',
                    'remove.user.adult',
                    'remove.user.child',
                    'save.game',
                    'update.password',
                    'update.skribble',
                    'view.profile.image',
                    'view.skribble',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                ],
            ],

            'me.adult' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'update.password',
                    'save.game',
                    'edit.user.adult',
                    'edit.user.child',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.user.adult',
                    'view.user.child',
                    'view.profile.image',
                    'attach.profile.image',
                ],
            ],

            'child' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'can.friend',
                    'child.code',
                    'create.user.flip',
                    'pick.username',
                    'update.password',
                    'view.flip',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                    'view.profile.image',
                    'view.user.flip',
                ],
            ],

            'student.child' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'can.friend',
                    'child.code',
                    'pick.username',
                    'update.password',
                    'view.games',
                    'view.flip',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                    'view.profile.image',
                    'view.user.flip',
                    'view.user.groups',
                ],
            ],

            'logged_in.child' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'parents'     => ['group_admin'],
                'permissions' => [
                    'update.password',
                    'view.games',
                    'view.user.groups',
                    'view.group.users',
                    'view.flip',
                    'pick.username',
                    'view.user.adult',
                    'view.user.child',
                ],
            ],

            'logged_in.adult' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'parents'     => ['group_admin'],
                'permissions' => [
                    'update.password',
                    'view.games',
                    'view.user.orgs',
                    'view.user.groups',
                    'pick.username',
                    'view.user.adult',
                    'view.user.child',
                ],
            ],

            'guest' => [
                'parents' => ['logged_in.adult'],
            ],
        ],
    ],
];
