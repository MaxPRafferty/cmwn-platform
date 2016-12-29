<?php

namespace ApplicationTest\Service;

use Application\Service\AbstractTableFactory;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

/**
 * Test AbstractTableFactoryTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractTableFactoryTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|Adapter
     */
    protected $adapter;

    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @before
     */
    public function setUpAdapter()
    {
        $this->adapter = \Mockery::mock(Adapter::class);
        $this->adapter->shouldReceive('getPlatform')->byDefault();
    }

    /**
     * @before
     */
    public function setUpServiceManager()
    {
        $this->services = new ServiceManager();
        $this->services->setService(Adapter::class, $this->adapter);
        $this->services->addAbstractFactory(new AbstractTableFactory());
    }

    /**
     * @test
     *
     * @param string $tableName
     * @param string $serviceName
     *
     * @dataProvider validTableServicesProvider
     */
    public function testItShouldBuildTableGateways(string $tableName, string $serviceName)
    {
        $this->assertTrue(
            $this->services->has($serviceName),
            AbstractTableFactory::class . ' did not report that ' . $serviceName . ' can be built'
        );

        /** @var TableGateway $gateway */
        $gateway = $this->services->get($serviceName);
        $this->assertInstanceOf(
            TableGateway::class,
            $gateway,
            AbstractTableFactory::class . ' did not build a table gateway'
        );

        $this->assertEquals(
            $tableName,
            $gateway->table,
            AbstractTableFactory::class . ' did not set the correct table name'
        );
    }

    /**
     * @return array
     */
    public function validTableServicesProvider()
    {
        return [
            'table/camelCase' => [
                'camel_case',
                'table/CamelCase',
            ],

            'table/under_score' => [
                'under_score',
                'table/under_score',
            ],

            'TABLE/UPPER' => [
                'upper',
                'TABLE/UPPER',
            ],

            'table/lower' => [
                'lower',
                'table/lower',
            ],

            'tableno_slash' => [
                'no_slash',
                'tableno_slash',
            ],

            'BeforeTable' => [
                'before',
                'BeforeTable',
            ],

            'FlipsTable' => [
                'flips',
                'FlipsTable',
            ],
        ];
    }
}
