<?php

return [
    'cmwn-security' => [
        'open-routes'       => [
            'api.rest.token',
            'api.rest.login'
        ],
        'route-permissions' => [
            'api.rest.group' => [
                'GET'     => 'read.group',
                'POST'    => 'create.group',
                'PUT'     => 'edit.group',
                'DELETE'  => 'remote.group',
            ]
        ],
    ],
];
