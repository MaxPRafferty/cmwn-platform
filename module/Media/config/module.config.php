<?php

return [
    'service_manager' => [
        'aliases' => [
            \Media\Service\MediaServiceInterface::class => \Media\Service\MediaService::class,
        ],
        'factories' => [
            \Media\Service\MediaService::class => \Media\Service\MediaServiceFactory::class,
        ],
    ],
];
