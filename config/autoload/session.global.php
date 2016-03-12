<?php
$cacheHost = getenv('CACHE1_HOST');
$cachePort = getenv('CACHE1_PORT');
return [
    'session' => [
        'config' => [
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => [
                'name'            => 'cmwn',
                'cookie_httponly' => true,
                'cookie_secure'   => true
            ],
        ],
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'save_handler' => [
            'adapter' => [
                'name' => 'redis',
                'options' => [
                    'server' => 'tcp://' . $cacheHost . ':' . $cachePort,
                ],
            ],
        ],
        'validators' => [
            'Zend\Session\Validator\HttpUserAgent',
        ],
    ],
];