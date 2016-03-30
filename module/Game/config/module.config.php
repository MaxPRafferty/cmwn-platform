<?php

return [
    'service_manager' => [
        'aliases' => [
            'Game\Service' => 'Game\Service\GameService'
        ],
        'factories' => [
            'Game\Service\GameService' => 'Game\Service\GameServiceFactory'
        ],
    ],
];
