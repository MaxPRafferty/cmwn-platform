<?php

return [
    'cmwn-security' => [
        'open-routes'       => [
            'api.rest.token',
            'api.rest.login',
            'api.rest.forgot',
            'api.rest.logout',
            'api.rest.image',
        ],
        'route-permissions' => [
            'api.rest.flip'        => [
                'GET' => 'view.flip',
            ],
            'api.rest.user'        => [
                'GET'  => ['view.user.adult', 'view.user.child'],
                'POST' => 'create.user',
                'PUT'  => 'edit.user',
            ],
            'api.rest.org'         => [
                'GET'    => 'view.org',
                'POST'   => 'create.org',
                'PUT'    => 'edit.org',
                'DELETE' => 'delete.org',
            ],
            'api.rest.game'        => [
                'GET' => 'view.games',
            ],
            'api.rest.group'       => [
                'GET'    => 'view.group',
                'POST'   => 'create.group',
                'PUT'    => 'edit.group',
                'DELETE' => 'remote.group',
            ],
            'api.rest.password'    => [
                'POST' => 'update.password',
            ],
            'api.rest.group-users' => [
                'GET' => 'view.group.users',
            ],
            'api.rest.org-users'   => [
                'GET' => 'view.org.users',
            ],
            'api.rest.user-image'  => [],
            'api.rest.import'      => [
                'POST' => 'import',
            ],
            'api.rest.user-name'   => [
                'GET'  => 'pick.username',
                'POST' => 'pick.username',
            ],
        ],
    ],
];
