<?php

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
        'validators' => [
            'Zend\Session\Validator\HttpUserAgent',
        ],
    ],
];