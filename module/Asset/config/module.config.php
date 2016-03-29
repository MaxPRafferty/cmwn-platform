<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Image\Service'      => 'Asset\Service\ImageService',
            'User\Image\Service' => 'Asset\Service\UserImageService',
        ],
        'invokables' => [
            'Asset\Delegator\ImageDelegatorFactory'            => 'Asset\Delegator\ImageDelegatorFactory',
            'Asset\Delegator\UserImageServiceDelegatorFactory' => 'Asset\Delegator\UserImageServiceDelegatorFactory',
        ],
        'factories'  => [
            'Asset\Service\ImageService'     => 'Asset\Service\ImageServiceFactory',
            'Asset\Service\UserImageService' => 'Asset\Service\UserImageServiceFactory',
        ],
        'delegators' => [
            'Asset\Service\ImageService'     => [
                'Asset\Delegator\ImageDelegatorFactory',
            ],
            'Asset\Service\UserImageService' => [
                'Asset\Delegator\UserImageServiceDelegatorFactory',
            ],
        ],
    ],
];
