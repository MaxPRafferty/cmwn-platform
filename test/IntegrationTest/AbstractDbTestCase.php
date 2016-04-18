<?php

namespace IntegrationTest;

use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase as TestCase;

/**
 * Class AbstractDbTestCase
 *
 * ${CARET}
 */
abstract class AbstractDbTestCase extends TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
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
