<?php

return [
    'service_manager'        => [
        'factories' => [
            \Sa\V1\Rest\SuperAdminSettings\SuperAdminSettingsResource::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'router'                 => [
        'routes' => [
            'sa.rest.settings' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/sa/settings[/:settings_id]',
                    'defaults' => [
                        'controller' => 'Sa\\V1\\Rest\\SuperAdminSettings\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning'          => [
        'uri' => [
            'sa.rest.settings',
        ],
    ],
    'zf-rest'                => [
        'Sa\\V1\\Rest\\SuperAdminSettings\\Controller' => [
            'listener'                   => 'Sa\\V1\\Rest\\SuperAdminSettings\\SuperAdminSettingsResource',
            'route_name'                 => 'sa.rest.settings',
            'route_identifier_name'      => 'settings_id',
            'collection_name'            => 'settings',
            'entity_http_methods'        => [],
            'collection_http_methods'    => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => 'Sa\\V1\\Rest\\SuperAdminSettings\\SuperAdminSettingsEntity',
            'collection_class'           => 'Sa\\V1\\Rest\\SuperAdminSettings\\SuperAdminSettingsCollection',
            'service_name'               => 'SuperAdminSettings',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers'            => [
            'Sa\\V1\\Rest\\SuperAdminSettings\\Controller' => 'HalJson',
        ],
        'accept_whitelist'       => [
            'Sa\\V1\\Rest\\SuperAdminSettings\\Controller' => [
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'Sa\\V1\\Rest\\SuperAdminSettings\\Controller' => [
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal'                 => [
        'metadata_map' => [
            'Sa\\V1\\Rest\\SuperAdminSettings\\SuperAdminSettingsEntity' => [
                'route_name'             => 'sa.rest.settings',
                'route_identifier_name'  => 'settings_id',
                'hydrator'               => 'Zend\\Hydrator\\ArraySerializable',
            ],
        ],
    ],
];
