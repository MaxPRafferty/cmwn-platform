<?php

return [
    'cmwn-security' => [
        'basic-auth'        => [
            'config' => [
                'accept_schemes' => 'basic',
                'realm'          => 'cmwn',
            ],

            'resolver' => [
                'resolver_class' => \Zend\Authentication\Adapter\Http\FileResolver::class,
                'options'        => [
                    'file' => realpath(__DIR__ . '/../../data/files/.htpasswd-lambda'),
                ],
            ],
        ],
        'open-routes'       => [
            'api.rest.token',
            'api.rest.login',
            'api.rest.forgot',
            'api.rest.logout',
            'api.rest.image',
            'api.rest.media',
        ],
        'route-permissions' => [
            'api.rest.reset'           => [
                'POST' => ['adult.code', 'child.code'],
            ],
            'api.rest.flip'            => [
                'GET' => 'view.flip',
            ],
            'api.rest.flip-user'       => [
                'GET'  => 'view.user.flip',
                'POST' => 'create.user.flip',
            ],
            'api.rest.user'            => [
                'GET'    => ['view.user.adult', 'view.user.child'],
                'POST'   => 'create.user',
                'PUT'    => ['edit.user.child', 'edit.user.adult'],
                'DELETE' => ['remove.user.child', 'remove.user.adult'],
            ],
            'api.rest.org'             => [
                'GET'    => ['view.org', 'view.user.orgs'],
                'POST'   => 'create.org',
                'PUT'    => 'edit.org',
                'DELETE' => 'delete.org',
            ],
            'api.rest.game'            => [
                'GET'    => ['view.games', 'view.game', 'view.deleted.game'],
                'POST'   => 'create.game',
                'PUT'    => 'update.game',
                'DELETE' => 'delete.game',
            ],
            'api.rest.group'           => [
                'GET'    => ['view.group', 'view.user.groups'],
                'POST'   => 'create.group',
                'PUT'    => 'edit.group',
                'DELETE' => 'remote.group',
            ],
            'api.rest.password'        => [
                'POST' => 'update.password',
            ],
            'api.rest.update-password' => [
                'POST' => 'update.password',
            ],
            'api.rest.group-users'     => [
                'GET' => 'view.group.users',
            ],
            'api.rest.org-users'       => [
                'GET' => 'view.org.users',
            ],
            'api.rest.user-image'      => [
                'GET'  => 'view.profile.image',
                'POST' => 'attach.profile.image',
            ],
            'api.rest.import'          => [
                'POST' => 'import',
            ],
            'api.rest.user-name'       => [
                'GET'  => 'pick.username',
                'POST' => 'pick.username',
            ],
            'api.rest.friend'          => [
                'GET'  => 'can.friend',
                'POST' => 'can.friend',
            ],
            'api.rest.suggest'         => [
                'GET' => 'can.friend',
            ],
            'api.rest.save-game'       => [
                'GET'    => 'save.game',
                'POST'   => 'save.game',
                'DELETE' => 'save.game',
            ],
            'api.rest.skribble'        => [
                'GET'    => 'view.skribble',
                'POST'   => 'create.skribble',
                'PUT'    => 'update.skribble',
                'PATCH'  => 'update.skribble',
                'DELETE' => 'delete.skribble',
            ],
            'api.rest.skribble-notify' => [
                'POST' => 'skribble.notice',
            ],
            'api.rest.feed' => [
                'GET'  => 'view.feed',
            ],
            'api.rest.feed-user' => [
                'GET' => 'view.user.feed',
            ],
            'api.rest.game-data' => [
                'GET' => 'view.game-data',
            ],
            'api.rest.flag' => [
                'GET'    => ['view.all.flagged.images', 'view.flagged.image'],
                'POST'   => 'flag.image',
                'PUT'    => 'edit.flag',
                'DELETE' => 'delete.flag'
            ],
            'sa.rest.settings' => [
                'GET' => 'sa.settings',
            ],
            'api.rest.group-reset' => [
                'POST' => 'reset.group.code'
            ],
            'api.rest.address' => [
                'GET'    => ['view.all.addresses', 'view.address'],
                'POST'   => 'create.address',
                'PUT'    => 'update.address',
                'DELETE' => 'delete.address',
            ],
            'api.rest.group-address' => [
                'GET'    => 'view.all.group.addresses',
                'POST'   => 'attach.group.address',
                'DELETE' => 'detach.group.address',
            ],
            'api.rest.super-flag' => [
                'POST' => 'set.super',
            ],
            'api.rest.super' => [
                'GET'  => 'get.super.user',
            ],
        ],
    ],
];
