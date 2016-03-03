<?php

return [
    'service_manager' => [
        'factories' => [
            'Zend\Session\SessionManager'                       => 'Security\Session\SessionManagerFactory',
            'Security\Service\SecurityService'                  => 'Security\Service\SecurityServiceFactory',
            'Security\Authentication\CmwnAuthenticationAdapter' => 'Security\Authentication\CmwnAuthenticationAdapterFactory',
            'Security\Authentication\CmwnAuthenticationService' => 'Security\Authentication\CmwnAuthenticationServiceFactory',
        ]
    ]
];
