<?php
$cacheHost = getenv('CACHE1_HOST');
$cachePort = getenv('CACHE1_PORT');

$config = [
    'session' => [
        'config'       => [
            'class'   => 'Zend\Session\Config\SessionConfig',
            'options' => [
                'name'            => 'CMWN',
                'cookie_httponly' => true,
                'cookie_secure'   => true,
                'cookie_domain'   => '.changemyworldnow.com',
            ],
        ],
        'storage'      => 'Zend\Session\Storage\SessionArrayStorage',
        'save_handler' => [
            'adapter' => [
                'name'    => 'redis',
                'options' => [
                    'server' => 'tcp://' . $cacheHost . ':' . $cachePort,
                ],
            ],
        ],
        'validators'   => [
        ],
    ],
];

if (defined('TEST_MODE')) {
    unset($config['session']['save_handler']);
}

return $config;

