<?php

return [
    'suggestion-engine' => [
        'rules' => [
            'friend-rule'  =>  \Suggest\Rule\FriendRule::class,
            'type-rule'    =>  \Suggest\Rule\TypeRule::class,
            'me-rule'      =>  \Suggest\Rule\MeRule::class,
        ],
        'filters' => [
            'class-filter' =>  \Suggest\Filter\ClassFilter::class,
        ]
    ]
];
