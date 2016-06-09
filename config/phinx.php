<?php
// @codingStandardsIgnoreStart

$dbName = getenv('DATABASE1_NAME');
$dbHost = getenv('DATABASE1_HOST');
$dbUser = getenv('DATABASE1_USER');
$dbPass = getenv('DATABASE1_PASS');

$testHost = getenv('MYSQL_PORT_3306_TCP_ADDR') === false ? $dbHost : getenv('MYSQL_PORT_3306_TCP_ADDR');
$testUser = getenv('MYSQL_ENV_MYSQL_USER') === false ? $dbUser : getenv('MYSQL_ENV_MYSQL_USER');
$testPass = getenv('MYSQL_ENV_MYSQL_PASSWORD') === false ? $dbPass : getenv('MYSQL_ENV_MYSQL_PASSWORD');

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
            'host'    => $dbHost,
            'name'    => $dbName,
            'user'    => $dbUser,
            'pass'    => $dbPass,
        ],
        'test'                    => [
            'adapter' => 'mysql',
            'host'    => $testHost,
            'name'    => 'cmwn_test',
            'user'    => 'cmwn_user',
            'pass'    => 'cmwn_pass',
        ],
    ],
];

return $config;