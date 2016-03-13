<?php

return [
    'service_manager' => [
        'factories' => [
            'Job\Service\ResqueWorker' => 'Job\Service\ResqueWorkerFactory'
        ]
    ]
];
