<?php

$dbName = getenv('DATABASE1_NAME');
$dbHost = getenv('DATABASE1_HOST');
$dbUser = getenv('DATABASE1_USER');
$dbPass = getenv('DATABASE1_PASS');

$dbName = empty($dbName) ? 'cmwn' : $dbName;
$dbHost = empty($dbHost) ? 'localhost' : $dbHost;
$dbUser = empty($dbUser) ? 'cmwn_user' : $dbUser;
$dbPass = empty($dbPass) ? 'cmwn_pass123$' : $dbPass;

return [
    'db' => [
        'driver'   => 'Pdo',
        'dsn'      => 'mysql:dbname=' . $dbName . ';host=' . $dbHost,
        'database' => $dbName,
        'username' => $dbUser,
        'password' => $dbPass,
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
