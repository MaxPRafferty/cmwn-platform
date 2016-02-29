<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Api\\V1\\Rest\\User\\UserResource' => 'Api\\V1\\Rest\\User\\UserResourceFactory',
            'Api\\V1\\Rest\\Org\\OrgResource' => 'Api\\V1\\Rest\\Org\\OrgResourceFactory',
            'Api\\V1\\Rest\\Game\\GameResource' => 'Api\\V1\\Rest\\Game\\GameResourceFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'api.rest.user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\User\\Controller',
                    ),
                ),
            ),
            'api.rest.org' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/org[/:org_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Org\\Controller',
                    ),
                ),
            ),
            'api.rest.game' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/game[/:game_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Game\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'api.rest.user',
            1 => 'api.rest.org',
            2 => 'api.rest.game',
        ),
    ),
    'zf-rest' => array(
        'Api\\V1\\Rest\\User\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\User\\UserResource',
            'route_name' => 'api.rest.user',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'user',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'type',
                1 => 'page',
                2 => 'per_page',
                3 => 'deleted',
                4 => 'username',
                5 => 'email',
                6 => 'first_name',
                7 => 'last_name',
                8 => 'middle_name',
                9 => 'gender',
            ),
            'page_size' => 25,
            'page_size_param' => 'page',
            'entity_class' => 'Api\\V1\\Rest\\User\\UserEntity',
            'collection_class' => 'Api\\V1\\Rest\\User\\UserCollection',
            'service_name' => 'User',
        ),
        'Api\\V1\\Rest\\Org\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Org\\OrgResource',
            'route_name' => 'api.rest.org',
            'route_identifier_name' => 'org_id',
            'collection_name' => 'org',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PUT',
                2 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Org\\OrgEntity',
            'collection_class' => 'Api\\V1\\Rest\\Org\\OrgCollection',
            'service_name' => 'Org',
        ),
        'Api\\V1\\Rest\\Game\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Game\\GameResource',
            'route_name' => 'api.rest.game',
            'route_identifier_name' => 'game_id',
            'collection_name' => 'game',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Game\\GameEntity',
            'collection_class' => 'Api\\V1\\Rest\\Game\\GameCollection',
            'service_name' => 'Game',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Api\\V1\\Rest\\User\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Org\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Game\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Api\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Org\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Game\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Api\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Org\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Game\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Api\\V1\\Rest\\User\\UserEntity' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.user',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\User\\UserCollection' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.user',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Org\\OrgEntity' => array(
                'entity_identifier_name' => 'org_id',
                'route_name' => 'api.rest.org',
                'route_identifier_name' => 'org_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Org\\OrgCollection' => array(
                'entity_identifier_name' => 'org_id',
                'route_name' => 'api.rest.org',
                'route_identifier_name' => 'org_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Game\\GameEntity' => array(
                'entity_identifier_name' => 'game_id',
                'route_name' => 'api.rest.game',
                'route_identifier_name' => 'game_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Game\\GameCollection' => array(
                'entity_identifier_name' => 'game_id',
                'route_name' => 'api.rest.game',
                'route_identifier_name' => 'game_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'Api\\V1\\Rest\\User\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\User\\Validator',
        ),
        'Api\\V1\\Rest\\Org\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Org\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'Api\\V1\\Rest\\User\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'first_name',
                'description' => 'Users First name',
                'error_message' => 'First name is invalid',
            ),
            1 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'middle_name',
                'description' => 'Users Middle Name',
                'error_message' => 'Middle name is invalid',
            ),
            2 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'last_name',
                'description' => 'Users Last name',
                'error_message' => 'Last name is invalid',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'gender',
                'description' => 'Users Gender',
                'error_message' => 'Invalid Gender',
            ),
            4 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(
                    0 => array(
                        'name' => 'Application\\Utils\\MetaFilter',
                        'options' => array(),
                    ),
                ),
                'name' => 'meta',
                'description' => 'meta data',
            ),
            5 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'User\\TypeValidator',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'type',
                'description' => 'The type of user',
            ),
            6 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'username',
                'description' => 'Users name',
                'error_message' => 'Invalid Username',
            ),
            7 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\EmailAddress',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'email',
                'description' => 'Users Email',
                'error_message' => 'Invalid Email',
            ),
            8 => array(
                'required' => false,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\Date',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'birthdate',
                'description' => 'birthdate',
                'error_message' => 'Invalid Birthdate',
            ),
        ),
        'Api\\V1\\Rest\\Org\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'title',
                'description' => 'Organizations title',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'description',
                'description' => 'Organizations description',
            ),
            2 => array(
                'required' => false,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'type',
                'description' => 'Type of organization',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'meta',
                'description' => 'Meta data for the organization',
            ),
        ),
    ),
);
