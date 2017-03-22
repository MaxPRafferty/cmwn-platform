<?php

return [
    'organizations' => [
        [
            'org_id'      => 'district',
            'title'       => 'Gina\'s District',
            'description' => null,
            'meta'        => null,
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,
            'type'        => 'district',
        ],
    ],
    'groups'        => [
        [
            'group_id'        => 'school',
            'organization_id' => 'district',
            'title'           => 'Gina\'s School',
            'description'     => null,
            'meta'            => null,
            'head'            => '1',
            'tail'            => '6',
            'created'         => '2016-04-15 15:46:07',
            'updated'         => '0000-00-00 00:00:00',
            'deleted'         => null,
            'type'            => 'school',
            'external_id'     => null,
            'parent_id'       => null,
            'depth'           => '0',
            'network_id'      => 'school',
        ],
    ],
    'users'         => [
        [
            'user_id'     => 'super_user',
            'username'    => 'super_user',
            'email'       => 'super@ginasink.com',
            'code'        => null,
            'type'        => 'ADULT',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'Joni',
            'middle_name' => null,
            'last_name'   => 'Albers',
            'gender'      => 'F',
            'meta'        => null,
            'birthdate'   => '2016-04-27 10:48:42',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '1',
            'external_id' => null,
        ],
    ],
    'names'         => [],
];
