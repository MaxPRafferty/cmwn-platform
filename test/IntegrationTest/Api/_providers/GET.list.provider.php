<?php

return [
    'English Teacher' => [
        'login'        => 'english_teacher',
        'expected_ids' => [
            'english_student',
        ],
    ],

    'Math Teacher' => [
        'login'        => 'math_teacher',
        'expected_ids' => [
            'math_student',
        ],
    ],

    'English Student' => [
        'login'        => 'english_student',
        'expected_ids' => [
            'english_teacher',
        ],
    ],

    'Math Student' => [
        'login'        => 'math_student',
        'expected_ids' => [
            'math_teacher',
        ],
    ],

    'Principal' => [
        'login'        => 'principal',
        'expected_ids' => [
            'math_student',
            'math_teacher',
            'english_student',
            'english_teacher',
        ],
    ],

    'Other Student' => [
        'login'        => 'other_student',
        'expected_ids' => [
            'other_teacher',
        ],
    ],

    'Other Teacher' => [
        'login'        => 'other_teacher',
        'expected_ids' => [
            'other_student',
        ],
    ],

    'Other Principal' => [
        'login'        => 'other_principal',
        'expected_ids' => [
            'other_student',
            'other_teacher',
        ],
    ],
    
    'Super' => [
        'login'        => 'super_user',
        'expected_ids' => [
            'other_student',
            'other_teacher',
            'other_principal',
            'math_student',
            'math_teacher',
            'english_student',
            'english_teacher',
            'principal',
        ],
    ],
];
