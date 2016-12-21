<?php

return [
    'session_config'  => [
        'config_class'    => \Zend\Session\Config\SessionConfig::class,
        'name'            => 'CMWN',
        'cookie_lifetime' => 259200,
        'cookie_httponly' => true,
        'cookie_secure'   => true,
        'cookie_domain'   => '.changemyworldnow.com',
    ],
    'session_storage' => [
        'type' => \Zend\Session\Storage\SessionArrayStorage::class,
    ],
];
