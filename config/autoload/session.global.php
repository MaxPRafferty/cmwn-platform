<?php

return [
    'session' => [
        'config'     => [
            'class'   => \Zend\Session\Config\SessionConfig::class,
            'options' => [
                'name'            => 'CMWN',
                'cookie_lifetime' => 259200,
                'cookie_httponly' => true,
                'cookie_secure'   => true,
                'cookie_domain'   => '.changemyworldnow.com',
            ],
        ],
        'storage'    => \Zend\Session\Storage\SessionArrayStorage::class,
        'validators' => [],
    ],
];
