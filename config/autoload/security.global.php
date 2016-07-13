<?php

return [
    'cmwn-security' => [
        'open-routes'       => [
            'api.rest.token',
            'api.rest.login',
            'api.rest.forgot',
            'api.rest.logout',
            'api.rest.image',
            'api.rest.media',
            'api.rest.skribble', // TODO lock this down
            'api.rest.skribble-notify', // TODO lock this down
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
                'GET'  => ['view.user.adult', 'view.user.child'],
                'POST' => 'create.user',
                'PUT'  => ['edit.user.child', 'edit.user.adult'],
            ],
            'api.rest.org'             => [
                'GET'    => 'view.org',
                'POST'   => 'create.org',
                'PUT'    => 'edit.org',
                'DELETE' => 'delete.org',
            ],
            'api.rest.game'            => [
                'GET' => 'view.games',
            ],
            'api.rest.group'           => [
                'GET'    => 'view.group',
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
                'GET'  => 'attach.profile.image',
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
                'DELETE' => 'delete.skribble',
            ],
        ],
    ],
];
