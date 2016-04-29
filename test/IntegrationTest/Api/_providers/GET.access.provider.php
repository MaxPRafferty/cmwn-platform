<?php

return [
    // English Student
    'English Teacher -> English Student' => [
        'login'  => 'english_teacher',
        'access' => 'english_student',
        'code'   => 200,
    ],

    'Math Teacher -> English Student' => [
        'login'  => 'math_teacher',
        'access' => 'english_student',
        'code'   => 403,
    ],

    'Principal -> English Student' => [
        'login'  => 'principal',
        'access' => 'english_student',
        'code'   => 200,
    ],

    'Math Student -> English Student' => [
        'login'  => 'math_student',
        'access' => 'english_student',
        'code'   => 200,
    ],

    'English Student -> English Student' => [
        'login'  => 'english_student',
        'access' => 'english_student',
        'code'   => 200,
    ],

    'Other Principal -> English Student' => [
        'login'  => 'other_principal',
        'access' => 'english_student',
        'code'   => 403,
    ],

    'Other Student -> English Student' => [
        'login'  => 'other_student',
        'access' => 'english_student',
        'code'   => 403,
    ],

    'Other Teacher -> English Student' => [
        'login'  => 'other_teacher',
        'access' => 'english_student',
        'code'   => 403,
    ],

    'Super -> English Student'        => [
        'login'  => 'super_user',
        'access' => 'english_student',
        'code'   => 200,
    ],


    // Math Student
    'English Teacher -> Math Student' => [
        'login'  => 'english_teacher',
        'access' => 'math_student',
        'code'   => 403,
    ],

    'Math Teacher -> Math Student' => [
        'login'  => 'math_teacher',
        'access' => 'math_student',
        'code'   => 200,
    ],

    'Principal -> Math Student' => [
        'login'  => 'principal',
        'access' => 'math_student',
        'code'   => 200,
    ],

    'Math Student -> Math Student' => [
        'login'  => 'math_student',
        'access' => 'math_student',
        'code'   => 200,
    ],

    'English Student -> Math Student' => [
        'login'  => 'english_student',
        'access' => 'math_student',
        'code'   => 200,
    ],

    'Other Principal -> Math Student' => [
        'login'  => 'other_principal',
        'access' => 'math_student',
        'code'   => 403,
    ],

    'Other Student -> Math Student' => [
        'login'  => 'other_student',
        'access' => 'math_student',
        'code'   => 403,
    ],

    'Other Teacher -> Math Student' => [
        'login'  => 'other_teacher',
        'access' => 'math_student',
        'code'   => 403,
    ],

    'Super -> Math Student'              => [
        'login'  => 'super_user',
        'access' => 'math_student',
        'code'   => 200,
    ],

    // English Teacher
    'English Teacher -> English Teacher' => [
        'login'  => 'english_teacher',
        'access' => 'english_teacher',
        'code'   => 200,
    ],

    'Math Teacher -> English Teacher' => [
        'login'  => 'math_teacher',
        'access' => 'english_teacher',
        'code'   => 200,
    ],

    'Principal -> English Teacher' => [
        'login'  => 'principal',
        'access' => 'english_teacher',
        'code'   => 200,
    ],

    'Math Student -> English Teacher' => [
        'login'  => 'math_student',
        'access' => 'english_teacher',
        'code'   => 403,
    ],

    'English Student -> English Teacher' => [
        'login'  => 'english_student',
        'access' => 'english_teacher',
        'code'   => 200,
    ],

    'Other Principal -> English Teacher' => [
        'login'  => 'other_principal',
        'access' => 'english_teacher',
        'code'   => 403,
    ],

    'Other Student -> English Teacher' => [
        'login'  => 'other_student',
        'access' => 'english_teacher',
        'code'   => 403,
    ],

    'Other Teacher -> English Teacher' => [
        'login'  => 'other_teacher',
        'access' => 'english_teacher',
        'code'   => 403,
    ],

    'Super -> English Teacher'        => [
        'login'  => 'super_user',
        'access' => 'english_teacher',
        'code'   => 200,
    ],

    // Math Teacher
    'English Teacher -> Math Teacher' => [
        'login'  => 'english_teacher',
        'access' => 'math_teacher',
        'code'   => 200,
    ],

    'Math Teacher -> Math Teacher' => [
        'login'  => 'math_teacher',
        'access' => 'math_teacher',
        'code'   => 200,
    ],

    'Principal -> Math Teacher' => [
        'login'  => 'principal',
        'access' => 'math_teacher',
        'code'   => 200,
    ],

    'Math Student -> Math Teacher' => [
        'login'  => 'math_student',
        'access' => 'math_teacher',
        'code'   => 200,
    ],

    'English Student -> Math Teacher' => [
        'login'  => 'english_student',
        'access' => 'math_teacher',
        'code'   => 403,
    ],

    'Other Principal -> Math Teacher' => [
        'login'  => 'other_principal',
        'access' => 'math_teacher',
        'code'   => 403,
    ],

    'Other Student -> Math Teacher' => [
        'login'  => 'other_student',
        'access' => 'math_teacher',
        'code'   => 403,
    ],

    'Other Teacher -> Math Teacher' => [
        'login'  => 'other_teacher',
        'access' => 'math_teacher',
        'code'   => 403,
    ],

    'Super -> Math Teacher'        => [
        'login'  => 'super_user',
        'access' => 'math_teacher',
        'code'   => 200,
    ],

    // Principal
    'English Teacher -> Principal' => [
        'login'  => 'english_teacher',
        'access' => 'principal',
        'code'   => 200,
    ],

    'Math Teacher -> Principal' => [
        'login'  => 'math_teacher',
        'access' => 'principal',
        'code'   => 200,
    ],

    'Principal -> Principal' => [
        'login'  => 'principal',
        'access' => 'principal',
        'code'   => 200,
    ],

    'Math Student -> Principal' => [
        'login'  => 'math_student',
        'access' => 'principal',
        'code'   => 200,
    ],

    'English Student -> Principal' => [
        'login'  => 'english_student',
        'access' => 'principal',
        'code'   => 200,
    ],

    'Other Principal -> Principal' => [
        'login'  => 'other_principal',
        'access' => 'principal',
        'code'   => 403,
    ],

    'Other Student -> Principal' => [
        'login'  => 'other_student',
        'access' => 'principal',
        'code'   => 403,
    ],

    'Other Teacher -> Principal' => [
        'login'  => 'other_teacher',
        'access' => 'principal',
        'code'   => 403,
    ],

    'Super -> Principal'                 => [
        'login'  => 'super_user',
        'access' => 'principal',
        'code'   => 200,
    ],


    // Other Principal
    'English Teacher -> Other Principal' => [
        'login'  => 'english_teacher',
        'access' => 'other_principal',
        'code'   => 403,
    ],

    'Math Teacher -> Other Principal' => [
        'login'  => 'math_teacher',
        'access' => 'other_principal',
        'code'   => 403,
    ],

    'Principal -> Other Principal' => [
        'login'  => 'principal',
        'access' => 'other_principal',
        'code'   => 403,
    ],

    'Math Student -> Other Principal' => [
        'login'  => 'math_student',
        'access' => 'other_principal',
        'code'   => 403,
    ],

    'English Student -> Other Principal' => [
        'login'  => 'english_student',
        'access' => 'other_principal',
        'code'   => 403,
    ],

    'Other Principal -> Other Principal' => [
        'login'  => 'other_principal',
        'access' => 'other_principal',
        'code'   => 200,
    ],

    'Other Student -> Other Principal' => [
        'login'  => 'other_student',
        'access' => 'other_principal',
        'code'   => 200,
    ],

    'Other Teacher -> Other Principal' => [
        'login'  => 'other_teacher',
        'access' => 'other_principal',
        'code'   => 200,
    ],

    'Super -> Other Principal'         => [
        'login'  => 'super_user',
        'access' => 'other_principal',
        'code'   => 200,
    ],

    // Other Teacher
    'English Teacher -> Other Teacher' => [
        'login'  => 'english_teacher',
        'access' => 'other_teacher',
        'code'   => 403,
    ],

    'Math Teacher -> Other Teacher' => [
        'login'  => 'math_teacher',
        'access' => 'other_teacher',
        'code'   => 403,
    ],

    'Principal -> Other Teacher' => [
        'login'  => 'principal',
        'access' => 'other_teacher',
        'code'   => 403,
    ],

    'Math Student -> Other Teacher' => [
        'login'  => 'math_student',
        'access' => 'other_teacher',
        'code'   => 403,
    ],

    'English Student -> Other Teacher' => [
        'login'  => 'english_student',
        'access' => 'other_teacher',
        'code'   => 403,
    ],

    'Other Principal -> Other Teacher' => [
        'login'  => 'other_principal',
        'access' => 'other_teacher',
        'code'   => 200,
    ],

    'Other Student -> Other Teacher' => [
        'login'  => 'other_student',
        'access' => 'other_teacher',
        'code'   => 200,
    ],

    'Other Teacher -> Other Teacher' => [
        'login'  => 'other_teacher',
        'access' => 'other_teacher',
        'code'   => 200,
    ],

    'Super -> Other Teacher'           => [
        'login'  => 'super_user',
        'access' => 'other_teacher',
        'code'   => 200,
    ],

    // Other Student
    'English Teacher -> Other Student' => [
        'login'  => 'english_teacher',
        'access' => 'other_student',
        'code'   => 403,
    ],

    'Math Teacher -> Other Student' => [
        'login'  => 'math_teacher',
        'access' => 'other_student',
        'code'   => 403,
    ],

    'Principal -> Other Student' => [
        'login'  => 'principal',
        'access' => 'other_student',
        'code'   => 403,
    ],

    'Math Student -> Other Student' => [
        'login'  => 'math_student',
        'access' => 'other_student',
        'code'   => 403,
    ],

    'English Student -> Other Student' => [
        'login'  => 'english_student',
        'access' => 'other_student',
        'code'   => 403,
    ],

    'Other Principal -> Other Student' => [
        'login'  => 'other_principal',
        'access' => 'other_student',
        'code'   => 200,
    ],

    'Other Student -> Other Student' => [
        'login'  => 'other_student',
        'access' => 'other_student',
        'code'   => 200,
    ],

    'Other Teacher -> Other Student' => [
        'login'  => 'other_teacher',
        'access' => 'other_student',
        'code'   => 200,
    ],

    'Super -> Other Student' => [
        'login'  => 'super_user',
        'access' => 'other_student',
        'code'   => 200,
    ],
];
