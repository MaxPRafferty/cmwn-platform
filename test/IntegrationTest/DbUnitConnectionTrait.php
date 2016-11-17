<?php

namespace IntegrationTest;

use IntegrationTest\DataSets\ArrayDataSet;
use PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection as TestConnection;

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
     * @var ArrayDataSet
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
     * @return ArrayDataSet
     * @deprecated
     */
    public function getDataSet()
    {
        trigger_error(
            'This is now deprecated in the trait.  Each class MUST define a specific lean dataset',
            E_USER_DEPRECATED
        );
        if (static::$dataSet === null) {
            $data = include __DIR__ . '/DataSets/empty.dataset.php';
            static::$dataSet = new ArrayDataSet($data);
        }

        return static::$dataSet;
    }
}
