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
            'remove.user.adult'    => 'Delete an Adult user',
            'remove.user.child'    => 'Delete a Child user',
            'can.friend'           => 'Can friend users',
            'view.feed'            => 'Can view feed',
            'view.user.feed'       => 'View user feed',

            // Flip
            'create.user.flip'     => 'Earn a flip',
            'view.flip'            => 'View flip information',
            'view.user.flip'       => 'View Flips for a user',
            'create.flip'          => 'Create a new flip',
            'edit.flip'            => 'Update existing flip',
            'delete.flip'          => 'Delete a flip',

            // group
            'create.child.group'   => 'Create a sub group',
            'create.group'         => 'Create a group',
            'view.group'           => 'View Group',
            'view.all.child.groups'=> 'View all child groups from a parent',
            'view.child.groups'    => 'View the child groups from a parent',
            'view.user.groups'     => 'View Groups the user belongs too',
            'view.all.groups'      => 'View all Groups',
            'edit.group'           => 'Edit a Group',
            'import'               => 'Import Data file',
            'remove.child.group'   => 'Remove a child group',
            'remove.group'         => 'Remove a group',

            // user group
            'add.group.user'       => 'Add user to group',
            'remove.group.user'    => 'Remove user from group',
            'view.group.users'     => 'View Group users',
            'reset.group.code'     => 'Reset code for users of a group',

            // organizations
            'create.org'           => 'Create an Organization',
            'view.all.orgs'        => 'View all Organizations',
            'view.org'             => 'View an Organization',
            'view.user.orgs'       => 'View all Organizations the user belongs too',
            'view.org.users'       => 'View all users in an organization',
            'edit.org'             => 'Edit an Organization',
            'remove.org'           => 'Remove an Organization',

            // game
            'view.game-data'       => 'View Save Game Data of a game',
            'view.games'           => 'View all Games',
            'view.game'            => 'View a game',
            'create.game'          => 'Create a new game',
            'update.game'          => 'Update existing game details',
            'delete.game'          => 'delete game',
            'save.game'            => 'Save Game progress',
            'view.deleted.games'   => 'view all games including soft deleted ones',
            'attach.user.game'     => 'attach a game to user',
            'detach.user.game'     => 'detach a game from user',

            // misc
            'adult.code'           => 'Send adult reset code',
            'child.code'           => 'Send child reset code',
            'attach.profile.image' => 'Upload a profile image',
            'view.profile.image'   => 'View a users profile image',
            'flag.image'           => 'Can flag images',
            'view.all.flagged.images'  => 'Can view all flagged images',
            'edit.flag'            => 'Edit flag',
            'delete.flag'          => 'delete flag',
            'view.flagged.image'   => 'view a flagged image using flag_id',
            'set.super'            => 'update the super status of a user',


            // skribble
            'view.skribble'        => 'Read Skribbles',
            'create.skribble'      => 'Create Skribbles',
            'delete.skribble'      => 'Delete Skribbles',
            'update.skribble'      => 'Update Skribbles',
            'skribble.notice'      => 'Notify Skribble status',

            // god mode
            'sa.settings'          => 'View God Mode Admin Dashboard',
            'get.super.user'       => 'Fetch a super user',

            //address
            'view.all.addresses'   => 'View Addresses',
            'view.address'         => 'View a single address',
            'create.address'       => 'Create new address',
            'update.address'       => 'update address',
            'delete.address'       => 'delete address',
            'view.all.group.addresses' => 'View addresses of a group',
            'attach.group.address' => 'attach address to a group',
            'detach.group.address' => 'detach address from a group',

        ],
        'roles'             => [
            'super' => [
                'entity_bits' => [
                    'group.district'        => -1,
                    'group.school'          => -1,
                    'group.class'           => -1,
                    'organization.district' => -1,
                    'organization.school'   => -1,
                    'organization.class'    => -1,
                    'adult'                 => -1,
                    'child'                 => -1,
                    'me'                    => -1,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'attach.profile.image',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'delete.flag',
                    'edit.group',
                    'edit.org',
                    'edit.user.adult',
                    'edit.user.child',
                    'flag.image',
                    'import',
                    'remove.child.group',
                    'remove.group',
                    'remove.group.user',
                    'remove.org',
                    'remove.user.adult',
                    'remove.user.child',
                    'skribble.notice',
                    'update.password',
                    'view.all.flagged.images',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.all.users',
                    'view.all.child.groups',
                    'view.flagged.image',
                    'view.flip',
                    'create.flip',
                    'edit.flip',
                    'delete.flip',
                    'view.game-data',
                    'view.deleted.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                    'view.user.groups',
                    'view.user.orgs',
                    'view.feed',
                    'sa.settings',
                    'create.game',
                    'update.game',
                    'delete.game',
                    'reset.group.code',
                    'view.feed',
                    'set.super',
                    'get.super.user',
                    'view.all.addresses',
                    'view.address',
                    'create.address',
                    'update.address',
                    'delete.address',
                    'view.all.group.addresses',
                    'attach.group.address',
                    'detach.group.address',
                    'view.user.feed',
                    'attach.user.game',
                    'detach.user.game',
                ],
            ],

            'admin.adult'       => [
                'entity_bits' => [
                    'group.school' => Rbac::SCOPE_UPDATE | Rbac::SCOPE_CREATE,
                    'group.class'  => Rbac::SCOPE_UPDATE | Rbac::SCOPE_CREATE,
                    'me'           => Rbac::SCOPE_UPDATE,
                    'child'        => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'adult'        => Rbac::SCOPE_REMOVE,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'remove.group.user',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.child.groups',
                    'view.flip',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                    'view.user.groups',
                    'view.user.orgs',
                    'reset.group.code',
                    'view.address',
                    'create.address',
                    'update.address',
                    'delete.address',
                    'view.all.group.addresses',
                    'attach.group.address',
                    'detach.group.address',
                ],
                'db-role' => true,
            ],

            'principal.adult' => [
                'entity_bits' => [
                    'group.school' => Rbac::SCOPE_UPDATE,
                    'group.class'  => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'adult'        => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'child'        => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'me'           => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'remove.group.user',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.child.groups',
                    'view.flip',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                    'view.user.groups',
                    'view.user.orgs',
                    'reset.group.code',
                    'view.address',
                    'create.address',
                    'update.address',
                    'delete.address',
                    'view.all.group.addresses',
                    'attach.group.address',
                    'detach.group.address',
                ],
                'db-role' => true,
            ],

            'asst_principal.adult' => [
                'entity_bits' => [
                    'group.school' => Rbac::SCOPE_UPDATE,
                    'group.class'  => Rbac::SCOPE_UPDATE,
                    'adult'        => Rbac::SCOPE_REMOVE,
                    'child'        => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'me'           => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'remove.group.user',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.child.groups',
                    'view.flip',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                    'view.user.groups',
                    'view.user.orgs',
                    'reset.group.code',
                    'view.address',
                    'create.address',
                    'update.address',
                    'delete.address',
                    'view.all.group.addresses',
                    'attach.group.address',
                    'detach.group.address',
                ],
                'db-role' => true,
            ],

            'teacher.adult' => [
                'entity_bits' => [
                    'group.class' => Rbac::SCOPE_UPDATE,
                    'child'       => Rbac::SCOPE_UPDATE | Rbac::SCOPE_REMOVE,
                    'me'          => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'child.code',
                    'edit.group',
                    'edit.user.child',
                    'remove.user.child',
                    'view.flip',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                    'view.user.groups',
                    'view.user.orgs',
                    'reset.group.code',
                    'view.address',
                    'update.address',
                    'create.address',
                    'delete.address',
                    'view.all.group.addresses',
                    'attach.group.address',
                    'detach.group.address',
                ],
                'db-role' => true,
            ],

            'neighbor.adult' => [
                'entity_bits' => [],
                'permissions' => [
                    'view.flip',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.flip',
                ],
                'db-role' => false,
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
                    'edit.user.child',
                    'flag.image',
                    'pick.username',
                    'save.game',
                    'update.password',
                    'update.skribble',
                    'view.flip',
                    'view.games',
                    'view.profile.image',
                    'view.skribble',
                    'view.user.child',
                    'view.user.flip',
                    'view.user.groups',
                    'view.user.feed',
                    'view.address',
                    'view.all.group.addresses',
                ],
                'db-role' => false,
            ],

            'me.adult' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'attach.profile.image',
                    'edit.user.adult',
                    'flag.image',
                    'update.password',
                    'view.flip',
                    'view.games',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.flip',
                    'view.user.groups',
                    'view.user.orgs',
                    'view.user.feed',
                    'view.address',
                    'create.address',
                    'update.address',
                    'delete.address',
                    'view.all.group.addresses',
                    'attach.group.address',
                    'detach.group.address',
                ],
                'db-role' => false,
            ],

            'neighbor.child'    => [
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
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                ],
                'db-role' => false,
            ],

            'student.child' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'can.friend',
                    'view.flip',
                    'view.games',
                    'view.group',
                    'view.user.groups',
                    'view.group.users',
                    'view.profile.image',
                    'view.user.adult',
                    'view.user.child',
                    'view.user.flip',
                    'view.address',
                    'view.all.group.addresses',
                ],
                'db-role' => true,
            ],

            'logged_in.child' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'view.games',
                    'view.flip',
                ],
                'db-role' => false,
            ],

            'logged_in.adult' => [
                'entity_bits' => [
                    'me' => Rbac::SCOPE_UPDATE,
                ],
                'permissions' => [
                    'view.games',
                    'view.flip',
                ],
                'db-role' => false,
            ],

            'guest' => [
            ],
        ],
    ],
];
