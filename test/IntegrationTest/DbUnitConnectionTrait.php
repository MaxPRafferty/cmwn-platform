<?php

namespace IntegrationTest;

use IntegrationTest\DataSets\ArrayDataSet;
use PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection as TestConnection;
use PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet as MysqlDataSet;

/**
 * Trait DbUnitConnectionTrait
 */
trait DbUnitConnectionTrait
{
    /**
     * @var TestConnection
     */
    private $conn = null;

    /**
     * @var MysqlDataSet
     */
    protected static $dataSet;

    /**
     * @return TestConnection
     */
    public function getConnection()
    {
        if ($this->conn === null) {
            $config     = TestHelper::getTestDbConfig();
            $this->conn = new TestConnection(TestHelper::getPdoConnection(), $config['database']);
        }

        return $this->conn;
    }

    /**
     * @return MysqlDataSet
     */
    public function getDataSet()
    {
        if (static::$dataSet === null) {
            $data = include __DIR__ . '/DataSets/default.dataset.php';
            static::$dataSet = new ArrayDataSet($data);
        }
        
        return static::$dataSet;
    }
}
