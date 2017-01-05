<?php

return [
    'service_manager' => [
        'aliases'    => [
            'Image\Service'                                 => \Asset\Service\ImageService::class,
            'User\Image\Service'                            => \Asset\Service\UserImageService::class,
            \Asset\Service\UserImageServiceInterface::class => \Asset\Service\UserImageService::class,
            \Asset\Service\ImageServiceInterface::class     => \Asset\Service\ImageService::class,
        ],
        'factories'  => [
            \Asset\Service\ImageService::class     => \Asset\Service\ImageServiceFactory::class,
            \Asset\Service\UserImageService::class => \Asset\Service\UserImageServiceFactory::class,
            \Asset\Delegator\ImageDelegatorFactory::class            =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Asset\Delegator\UserImageServiceDelegatorFactory::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,

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
