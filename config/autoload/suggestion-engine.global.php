<?php

return [
    'suggestion-engine' => [
        'rules'   => [
            'me-rule'     => \Suggest\Rule\MeRule::class,
            'type-rule'   => \Suggest\Rule\TypeRule::class,
            'friend-rule' => \Suggest\Rule\FriendRule::class,
        ],
        'filters' => [
            'class-filter' => \Suggest\Filter\ClassFilter::class,
        ],
    ],
];
