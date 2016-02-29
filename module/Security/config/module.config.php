<?php

return [
    'service_manager' => [
        'factories' => [
            'Zend\Session\SessionManager' => 'Security\Factory\SessionManagerFactory'
        ]
    ]
];
