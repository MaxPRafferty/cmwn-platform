<?php

namespace IntegrationTest;

use PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection as TestConnection;
use PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet as MysqlDataSet;

/**
 * Trait DbUnitConnectionTrait
 */
trait DbUnitConnectionTrait
{
    /**
     * @var \PDO
     */
    static private $pdo = null;

    /**
     * @var TestConnection
     */
    private $conn = null;

    /**
     * @return TestConnection
     */
    public function getConnection()
    {
        if ($this->conn === null) {
            $config = TestHelper::getTestDbConfig();

            if (self::$pdo == null) {
                self::$pdo = new \PDO($config['dsn'], $config['username'], $config['password']);
            }

            $this->conn = new TestConnection(self::$pdo, $config['datbase']);
        }

        return $this->conn;
    }

    /**
     * @return MysqlDataSet
     */
    public function getDataSet()
    {
        return new MysqlDataSet(__DIR__ . '/DataSets/default.dataset.xml');
    }
}
