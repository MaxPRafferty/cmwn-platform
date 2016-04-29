<?php

namespace IntegrationTest;

use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase as TestCase;

/**
 * Class AbstractDbTestCase
 */
abstract class AbstractDbTestCase extends TestCase
{
    /**
     * @var null
     */
    static private $pdo = null;

    /**
     * @var null
     */
    private $conn = null;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if ($this->conn === null) {
            $config = TestHelper::getTestDbConfig();

            if (self::$pdo == null) {
                self::$pdo = new \PDO($config['dsn'], $config['username'], $config['password']);
            }

            $this->conn = $this->createDefaultDBConnection(self::$pdo, $config['datbase']);
        }

        return $this->conn;
    }
}
