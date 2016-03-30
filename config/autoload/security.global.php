<?php

return [
    'cmwn-security' => [
        'open-routes'       => [
            'api.rest.token',
            'api.rest.login',
            'api.rest.forgot',
            'api.rest.logout'
        ],
        'route-permissions' => [
            'api.rest.user'        => [
                'GET'   => 'view.user',
                'POST'  => 'create.user',
                'PUT'   => 'update.user',
            ],
            'api.rest.org'         => [
                'GET'    => 'view.org',
                'POST'   => 'create.org',
                'PUT'    => 'edit.org',
                'DELETE' => 'delete.org',
            ],
            'api.rest.game'        => [
                'GET'   => 'view.games',
            ],
            'api.rest.group'       => [
                'GET'    => 'read.group',
                'POST'   => 'create.group',
                'PUT'    => 'edit.group',
                'DELETE' => 'remote.group',
            ],
            'api.rest.password'    => [
                'POST'  => 'update.password',
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
        ],
    ],
];
