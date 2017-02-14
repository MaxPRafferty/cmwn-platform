<?php

return [

    'games' => [
        [
            'game_id'     => 'animal-id',
            'created'     => '2016-04-13 00:00:00',
            'updated'     => '2016-04-13 00:00:00',
            'title'       => 'Animal ID',
            'description' => 'Can you ID the different kinds of animals? Do you know what plants and animals
                    belong together? Prove it and learn it right here!
                ',
            'deleted'     => null,
            'meta'        => '{"desktop" : false, "unity" : false}',
            'coming_soon' => '0',
            'global'      => '1',
        ],
        [
            'game_id'     => 'be-bright',
            'created'     => '2016-04-13 00:00:00',
            'updated'     => '2016-04-13 00:00:00',
            'title'       => 'Be Bright',
            'description' => 'Become a Light Saver agent of change! This music video will kick your inner
                    superhero into high gear!
                ',
            'deleted'     => null,
            'meta'        => '{"desktop" : false, "unity" : false}',
            'coming_soon' => '0',
        ],
        [
            'game_id'     => 'Monarch',
            'created'     => '2016-04-13 00:00:00',
            'updated'     => '2016-04-13 00:00:00',
            'title'       => 'Monarch',
            'description' => 'Monarch Butterflies are crucial for the environment' .
                ' yet they are endangered! This is your spot!',
            'meta'        => '{"desktop" : false, "unity" : false}',
            'deleted'     => null,
            'coming_soon' => '0',
        ],
        [
            'game_id'     => 'deleted-game',
            'created'     => '2016-04-13 00:00:00',
            'updated'     => '2016-04-13 00:00:00',
            'title'       => 'Deleted Gmae',
            'description' => 'I deleted this :P',
            'meta'        => '{"desktop" : false, "unity" : false}',
            'deleted'     => '2016-04-13 00:00:00',
            'coming_soon' => '0',
        ],
    ],

    'users'      => [
        [
            'user_id'     => 'english_student',
            'username'    => 'english_student',
            'email'       => 'english_student@ginasink.com',
            'code'        => null,
            'type'        => 'CHILD',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'John',
            'middle_name' => 'D',
            'last_name'   => 'Yoder',
            'gender'      => 'M',
            'meta'        => null,
            'birthdate'   => '2016-04-15 11:58:15',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => '8675309',
        ],
        [
            'user_id'     => 'english_teacher',
            'username'    => 'english_teacher',
            'email'       => 'english_teacher@ginasink.com',
            'code'        => null,
            'type'        => 'ADULT',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'Angelot',
            'middle_name' => 'M',
            'last_name'   => 'Fredickson',
            'gender'      => 'M',
            'meta'        => '[]',
            'birthdate'   => '2016-04-15 00:00:00',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => null,
        ],
        [
            'user_id'     => 'math_student',
            'username'    => 'math_student',
            'email'       => 'math_student@ginasink.com',
            'code'        => null,
            'type'        => 'CHILD',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'WILLIS',
            'middle_name' => 'C',
            'last_name'   => 'KELSEY',
            'gender'      => 'M',
            'meta'        => null,
            'birthdate'   => '2016-04-15 11:50:47',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => null,
        ],
        [
            'user_id'     => 'math_teacher',
            'username'    => 'math_teacher',
            'email'       => 'math_teacher@ginasink.com',
            'code'        => null,
            'type'        => 'ADULT',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'William',
            'middle_name' => 'T',
            'last_name'   => 'West',
            'gender'      => 'M',
            'meta'        => null,
            'birthdate'   => '2016-04-15 11:50:05',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => null,
        ],
        [
            'user_id'     => 'other_principal',
            'username'    => 'other_principal',
            'email'       => 'other_principal@manchuck.com',
            'code'        => null,
            'type'        => 'ADULT',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'Max',
            'middle_name' => 'C',
            'last_name'   => 'Rafferty',
            'gender'      => 'M',
            'meta'        => null,
            'birthdate'   => '2016-04-15 11:51:42',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => null,
        ],
        [
            'user_id'     => 'other_student',
            'username'    => 'other_student',
            'email'       => 'other_student@manchuck.com',
            'code'        => null,
            'type'        => 'CHILD',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'Chuck',
            'middle_name' => 'C',
            'last_name'   => 'Reeves',
            'gender'      => 'M',
            'meta'        => null,
            'birthdate'   => '2016-04-15 11:51:42',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => null,
        ],
        [
            'user_id'     => 'other_teacher',
            'username'    => 'other_teacher',
            'email'       => 'other_teacher@manchuck.com',
            'code'        => null,
            'type'        => 'ADULT',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'Josh',
            'middle_name' => 'C',
            'last_name'   => 'Savino',
            'gender'      => 'M',
            'meta'        => null,
            'birthdate'   => '2016-04-15 11:51:42',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => null,
        ],
        [
            'user_id'     => 'principal',
            'username'    => 'principal',
            'email'       => 'principal@ginasink.com',
            'code'        => null,
            'type'        => 'ADULT',
            'password'    => '$2y$10$b53JWhhPjSyHvbvaL0aaD.9G3RKTd4pZn6JCkop6pkqFYDrEPJTC.',
            'first_name'  => 'Kirk',
            'middle_name' => 'S',
            'last_name'   => 'West',
            'gender'      => 'M',
            'meta'        => null,
            'birthdate'   => '2016-04-15 11:49:08',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,

            'super'       => '0',
            'external_id' => null,
        ],
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
    'user_saves' => [],
    'user_games' => [
        [
            'user_id' => 'english_student',
            'game_id' => 'Monarch',
        ],
        [
            'user_id' => 'math_student',
            'game_id' => 'animal-id'
        ],
    ],
];
