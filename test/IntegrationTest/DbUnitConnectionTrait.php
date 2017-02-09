<?php

namespace IntegrationTest;

use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Trait DbUnitConnectionTrait
 *
 * @group Db
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
     * @return \PHPUnit\DbUnit\Database\DefaultConnection
     */
    public function getConnection()
    {
        return TestHelper::getTestConnection();
    }
}
