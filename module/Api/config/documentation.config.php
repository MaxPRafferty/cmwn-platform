<?php
// This file is auto generated skipping PHPCS test
// @codingStandardsIgnoreStart
return array(
    'Api\\V1\\Rest\\User\\Controller' => array(
        'description' => 'Top level management for users',
        'collection' => array(
            'GET' => array(
                'response' => '{
   "_links": {
       "self": {
           "href": "/user"
       },
       "first": {
           "href": "/user?page={page}"
       },
       "prev": {
           "href": "/user?page={page}"
       },
       "next": {
           "href": "/user?page={page}"
       },
       "last": {
           "href": "/user?page={page}"
       }
   }
   "_embedded": {
       "user": [
           {
               "_links": {
                   "self": {
                       "href": "/user[/:user_id]"
                   }
               }
              "first_name": "Users First name",
              "middle_name": "Users Middle Name",
              "last_name": "Users Last name",
              "gender": "Users Gender",
              "meta": "meta data",
              "type": "The type of user",
              "username": "Users name",
              "email": "Users Email",
              "birthdate": "birthdate"
           }
       ]
   }
}',
            ),
        ),
    ),
);
