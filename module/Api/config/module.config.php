<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Api\\V1\\Rest\\User\\UserResource' => 'Api\\V1\\Rest\\User\\UserResourceFactory',
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
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'api.rest.user',
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
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Api\\V1\\Rest\\User\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Api\\V1\\Rest\\User\\Controller' => array(
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
        ),
    ),
    'zf-content-validation' => array(
        'Api\\V1\\Rest\\User\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\User\\Validator',
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
                'validators' => array(

                ),
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
    ),
);
