<?php

return [
    'service_manager' => [
        'aliases' => [
            'Group\Service' => 'Group\Service\GroupService',
            'Group\GroupService' => 'Group\Service\GroupService',
        ],
        'invokables' => [
            'Group\Delegator\GroupDelegatorFactory'             => 'Group\Delegator\GroupDelegatorFactory',
            'Group\Delegator\UserGroupServiceDelegatorFactory'  => 'Group\Delegator\UserGroupServiceDelegatorFactory',
        ],
        'factories' => [
            'Group\Service\GroupService'     => 'Group\Service\GroupServiceFactory',
            'Group\Service\UserGroupService' => 'Group\Service\UserGroupServiceFactory',
        ],
        'delegators' => [
            'Group\Service\GroupService' => [
                'Group\Delegator\GroupDelegatorFactory'
            ],
            'Group\Service\UserGroupService' => [
                'Group\Delegator\UserGroupServiceDelegatorFactory'
            ],
        ],
    ],
];
