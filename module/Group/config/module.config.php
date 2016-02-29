<?php

return [
    'service_manager' => [
        'aliases' => [
            'Group\Service' => 'Group\Service\GroupService'
        ],
        'invokables' => [
            'Group\Delegator\GroupDelegatorFactory'
        ],
        'factories' => [
            'Group\Service\GroupService' => 'Group\Service\GroupServiceFactory'
        ],
        'delegators' => [
            'Group\Service\GroupService' => [
                'Group\Delegator\GroupDelegatorFactory'
            ],
        ],
    ],
];
