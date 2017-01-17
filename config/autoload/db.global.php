<?php
return [
    'db' => [
        'driver'   => 'Pdo',
        'dsn'      => 'mysql:dbname=cmwn;host=127.0.0.1',
        'database' => 'cmwn',
        'username' => 'cmwn_user',
        'password' => 'cmwn_pass123$',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
];
