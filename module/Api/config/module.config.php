<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Api\\V1\\Rest\\User\\UserResource' => 'Api\\V1\\Rest\\User\\UserResourceFactory',
            'Api\\V1\\Rest\\Org\\OrgResource' => 'Api\\V1\\Rest\\Org\\OrgResourceFactory',
            'Api\\V1\\Rest\\Game\\GameResource' => 'Api\\V1\\Rest\\Game\\GameResourceFactory',
            'Api\\V1\\Rest\\Image\\ImageResource' => 'Api\\V1\\Rest\\Image\\ImageResourceFactory',
            'Api\\V1\\Rest\\Group\\GroupResource' => 'Api\\V1\\Rest\\Group\\GroupResourceFactory',
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
            'api.rest.image' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/image[/:image_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Image\\Controller',
                    ),
                ),
            ),
            'api.rest.group' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/group[/:group_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Group\\Controller',
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
            3 => 'api.rest.image',
            4 => 'api.rest.group',
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
            'page_size_param' => 'per_page',
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
            'collection_query_whitelist' => array(
                0 => 'page',
                1 => 'per_page',
            ),
            'page_size' => 25,
            'page_size_param' => 'per_page',
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
            'collection_query_whitelist' => array(
                0 => 'page',
                1 => 'per_page',
            ),
            'page_size' => 25,
            'page_size_param' => 'per_page',
            'entity_class' => 'Api\\V1\\Rest\\Game\\GameEntity',
            'collection_class' => 'Api\\V1\\Rest\\Game\\GameCollection',
            'service_name' => 'Game',
        ),
        'Api\\V1\\Rest\\Image\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Image\\ImageResource',
            'route_name' => 'api.rest.image',
            'route_identifier_name' => 'image_id',
            'collection_name' => 'image',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => 'page',
            'entity_class' => 'Api\\V1\\Rest\\Image\\ImageEntity',
            'collection_class' => 'Api\\V1\\Rest\\Image\\ImageCollection',
            'service_name' => 'Image',
        ),
        'Api\\V1\\Rest\\Group\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Group\\GroupResource',
            'route_name' => 'api.rest.group',
            'route_identifier_name' => 'group_id',
            'collection_name' => 'group',
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
                0 => 'page',
                1 => 'per_page',
            ),
            'page_size' => 25,
            'page_size_param' => 'per_page',
            'entity_class' => 'Api\\V1\\Rest\\Group\\GroupEntity',
            'collection_class' => 'Api\\V1\\Rest\\Group\\GroupCollection',
            'service_name' => 'Group',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Api\\V1\\Rest\\User\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Org\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Game\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Image\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Group\\Controller' => 'HalJson',
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
            'Api\\V1\\Rest\\Image\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Group\\Controller' => array(
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
            'Api\\V1\\Rest\\Image\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Group\\Controller' => array(
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
            'Api\\V1\\Rest\\Image\\ImageEntity' => array(
                'entity_identifier_name' => 'image_id',
                'route_name' => 'api.rest.image',
                'route_identifier_name' => 'image_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Image\\ImageCollection' => array(
                'entity_identifier_name' => 'image_id',
                'route_name' => 'api.rest.image',
                'route_identifier_name' => 'image_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Group\\GroupEntity' => array(
                'entity_identifier_name' => 'group',
                'route_name' => 'api.rest.group',
                'route_identifier_name' => 'group_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Group\\GroupCollection' => array(
                'entity_identifier_name' => 'group',
                'route_name' => 'api.rest.group',
                'route_identifier_name' => 'group_id',
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
        'Api\\V1\\Rest\\Image\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Image\\Validator',
        ),
        'Api\\V1\\Rest\\Group\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Group\\Validator',
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
                'filters' => array(
                    0 => array(
                        'name' => 'Application\\Utils\\MetaFilter',
                        'options' => array(),
                    ),
                ),
                'name' => 'meta',
                'description' => 'Meta data for the organization',
            ),
        ),
        'Api\\V1\\Rest\\Image\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'image_id',
                'description' => 'The Image Id',
                'error_message' => 'Invalid Image Id',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\Uri',
                        'options' => array(
                            'allowRelative' => false,
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'url',
                'description' => 'Url for the image',
                'error_message' => 'Invalid URL',
            ),
        ),
        'Api\\V1\\Rest\\Group\\Validator' => array(
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
                'name' => 'organization_id',
                'description' => 'The Id of the organization this group belongs too',
                'error_message' => 'Invalid Organization or not found',
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
                'name' => 'title',
                'description' => 'The title for this Organization',
                'error_message' => 'Invalid Title',
            ),
            2 => array(
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
                'error_message' => 'Invalid Description',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'meta',
                'description' => 'Meta data for the group',
                'error_message' => 'Invalid Meta data',
            ),
        ),
    ),
);
