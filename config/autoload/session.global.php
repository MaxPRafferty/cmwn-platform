<?php
$cacheHost = getenv('CACHE1_HOST');
$cachePort = getenv('CACHE1_PORT');

$cacheHost = empty($cacheHost) ? 'localhost' : $cacheHost;
$cachePort = empty($cachePort) ? 6379 : $cachePort;

$config = [
    'session' => [
        'config'       => [
            'class'   => 'Zend\Session\Config\SessionConfig',
            'options' => [
                'name'            => 'CMWN',
                'cookie_lifetime' => 259200,
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
                    'ttl'     => 259200,
                ],
            ],
        ],
        'validators'   => [
        ],
    ],
];

if (defined('TEST_MODE') || !extension_loaded('redis')) {
    unset($config['session']['save_handler']);
}

return $config;
