<?php

return [
    'service_manager' => [
        'aliases' => [
            'Image\Service' => 'Asset\Service\ImageService'
        ],
        'invokables' => [
            'Asset\Delegator\ImageDelegatorFactory'
        ],
        'factories' => [
            'Asset\Service\ImageService' => 'Asset\Service\ImageServiceFactory'
        ],
        'delegators' => [
            'Asset\Service\ImageService' => [
                'Asset\Delegator\ImageDelegatorFactory'
            ],
        ],
    ],
];
