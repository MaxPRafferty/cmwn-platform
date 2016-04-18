<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Image\Service'      => \Asset\Service\ImageService::class,
            'User\Image\Service' => \Asset\Service\UserImageService::class,
        ],
        'invokables' => [
            \Asset\Delegator\ImageDelegatorFactory::class            => \Asset\Delegator\ImageDelegatorFactory::class,
            \Asset\Delegator\UserImageServiceDelegatorFactory::class => \Asset\Delegator\UserImageServiceDelegatorFactory::class,
        ],
        'factories'  => [
            \Asset\Service\ImageService::class     => \Asset\Service\ImageServiceFactory::class,
            \Asset\Service\UserImageService::class => \Asset\Service\UserImageServiceFactory::class,
        ],
        'delegators' => [
            \Asset\Service\ImageService::class     => [
                \Asset\Delegator\ImageDelegatorFactory::class,
            ],
            \Asset\Service\UserImageService::class => [
                \Asset\Delegator\UserImageServiceDelegatorFactory::class,
            ],
        ],
    ],
];
