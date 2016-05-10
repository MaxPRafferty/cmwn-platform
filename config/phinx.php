<?php
// @codingStandardsIgnoreStart

$dbName = getenv('DATABASE1_NAME');
$dbHost = getenv('DATABASE1_HOST');
$dbUser = getenv('DATABASE1_USER');
$dbPass = getenv('DATABASE1_PASS');

$testName = getenv('MYSQL_ENV_MYSQL_DATABASE') === false ? 'cmwn_test' : getenv('MYSQL_ENV_MYSQL_DATABASE');
$testHost = getenv('MYSQL_PORT_3306_TCP_ADDR') === false ? 'localhost' : getenv('MYSQL_PORT_3306_TCP_ADDR');
$testUser = getenv('MYSQL_ENV_MYSQL_ROOT_USERNAME') === false ? 'cmwn_user' : getenv('MYSQL_ENV_MYSQL_ROOT_USERNAME');
$testPass = getenv('MYSQL_ENV_MYSQL_ROOT_PASSWORD') === false ? 'cmwn_pass123$' : getenv('MYSQL_ENV_MYSQL_ROOT_PASSWORD');

$config = [
    'paths'        => [
        'migrations' => realpath(__DIR__ . '/../data/migrations'),
        'seeds'      => realpath(__DIR__ . '/../data/seeds'),
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database'        => $dbName,
        'prod'                    => [
            'adapter' => 'mysql',
            'host'    => $dbHost,
            'name'    => $dbName,
            'user'    => $dbUser,
            'pass'    => $dbPass,
        ],
        'dev'                     => [
            'adapter' => 'mysql',
            'host'    => 'localhost',
            'name'    => 'cmwn',
            'user'    => 'cmwn_user',
            'pass'    => 'cmwn_pass123$',
        ],
        'test'                    => [
            'adapter' => 'mysql',
            'host'    => $testHost,
            'name'    => $testName,
            'user'    => $testUser,
            'pass'    => $testPass,
        ],
    ],
];

var_dump($config);
die();
return $config;