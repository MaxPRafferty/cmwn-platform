<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Zend\Authentication\Storage\Session::class => [
            'Auth\Name',
            'Auth\Member',
            \Zend\Session\SessionManager::class,
        ],
    ],

    'service_manager' => [
        'aliases'  => [
            \Zend\Authentication\Storage\StorageInterface::class => \Zend\Authentication\Storage\Session::class,
        ],
        'services' => [
            'Auth\Name'   => 'cmwn',
            'Auth\Member' => 'cmwn',
        ],
    ],
];
