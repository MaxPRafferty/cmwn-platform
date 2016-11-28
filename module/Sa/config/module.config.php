<?php

return [
    'shared-listeners' => [
        \Sa\Listeners\SaSettingsLinkListener::class,
    ],
    'service_manager' => [
        'factories' => [
            'Sa\\Rest\\SuperAdminSettings\\SuperAdminSettingsResource'
                => 'Sa\\Rest\\SuperAdminSettings\\SuperAdminSettingsResourceFactory',
        ],
        'invokables' => [
            \Sa\Listeners\SaSettingsLinkListener::class => \Sa\Listeners\SaSettingsLinkListener::class,
        ],
    ],
    'router' => [
        'routes' => [
            'sa.rest.settings' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/sa/settings[/:settings_id]',
                    'defaults' => [
                        'controller' => 'Sa\\Rest\\SuperAdminSettings\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            'sa.rest.settings',
        ],
    ],
    'zf-rest' => [
        'Sa\\Rest\\SuperAdminSettings\\Controller' => [
            'listener' => 'Sa\\Rest\\SuperAdminSettings\\SuperAdminSettingsResource',
            'route_name' => 'sa.rest.settings',
            'route_identifier_name' => 'settings_id',
            'collection_name' => 'settings',
            'entity_http_methods' => [],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => 'per_page',
            'entity_class' => 'Sa\\Rest\\SuperAdminSettings\\SuperAdminSettingsEntity',
            'collection_class' => 'Sa\\Rest\\SuperAdminSettings\\SuperAdminSettingsCollection',
            'service_name' => 'SuperAdminSettings',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'Sa\\Rest\\SuperAdminSettings\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'Sa\\Rest\\SuperAdminSettings\\Controller' => [
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'Sa\\Rest\\SuperAdminSettings\\Controller' => [
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            'Sa\\Rest\\SuperAdminSettings\\SuperAdminSettingsEntity' => [
                'entity_identifier_name' => 'settings_id',
                'route_name' => 'api.rest.sa.settings',
                'route_identifier_name' => 'settings_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ],
        ],
    ],
];
