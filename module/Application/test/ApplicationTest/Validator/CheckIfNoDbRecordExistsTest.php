<?php

namespace ApplicationTest\Validator;

use Application\Validator\CheckIfNoDbRecordExists;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Pdo\Connection;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\Adapter\Driver\Pdo\Statement;
use Zend\Db\Adapter\Platform\Mysql;
use Application\Exception\RuntimeException;

/**
 * Unit tests for CheckIfNoDbRecordExists
 */
class CheckIfNoDbRecordExistsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var CheckIfNoDbRecordExists
     */
    protected $validator;

    /**
     * @var \Mockery\MockInterface | Connection
     */
    protected $connection;

    /**
     * @var Result
     */
    protected $result;

    /**
     * @var \Mockery\MockInterface | Adapter
     */
    protected $adapter;

    /**
     * @before
     */
    public function setUpValidator()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\Driver\DriverInterface $driver */
        $driver = \Mockery::mock(DriverInterface::class);

        $statement = \Mockery::mock(Statement::class);

        $this->result = \Mockery::mock(Result::class);

        $driver->shouldReceive('getConnection')->andReturn($this->connection)->byDefault();
        $driver->shouldReceive('createStatement')->andReturn($statement);
        $driver->shouldReceive('formatParameterName')->andReturn('foo')->byDefault();

        $statement->shouldReceive('execute')->andReturn($this->result)->byDefault();
        $statement->shouldReceive('getParameterContainer')->byDefault();
        $statement->shouldReceive('setParameterContainer')->byDefault();
        $statement->shouldReceive('setSql')->byDefault();
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $this->adapter = \Mockery::mock(Adapter::class);
        $this->adapter->shouldReceive('getPlatform')->andReturn(new Mysql())->byDefault();
        $this->adapter->shouldReceive('getDriver')->andReturn($driver)->byDefault();
    }

    /**
     * @before
     */
    public function setUpConnection()
    {
        $this->connection = \Mockery::mock(Connection::class);
    }

    /**
     * @test
     */
    public function testItShouldReturnTrueIfRecordDoesNotExist()
    {
        $this->validator = new CheckIfNoDbRecordExists([
            'table' => 'foo',
            'adapter' => $this->adapter,
            'field' => 'bar',
        ]);

        $this->result->shouldReceive('current')->andReturn(null)->once();
        $this->assertTrue($this->validator->isValid('value', ['cfield' => 'cvalue']), 'Did not validate correctly');
    }

    /**
     * @test
     */
    public function testItShouldReturnFalseIfRecordExists()
    {
        $this->validator = new CheckIfNoDbRecordExists([
            'table' => 'foo',
            'adapter' => $this->adapter,
            'field' => 'bar',
        ]);

        $this->result->shouldReceive('current')->andReturn(true)->once();
        $this->assertFalse($this->validator->isValid('value', ['cfield' => 'cvalue']), 'Did not validate correctly');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionIfExcludeIsSetAnd()
    {
        $this->expectException(RuntimeException::class);
        $this->validator = new CheckIfNoDbRecordExists([
            'table' => 'foo',
            'adapter' => $this->adapter,
            'field' => 'bar',
            'exclude' => [
                'field' => 'baz',
            ]
        ]);
    }

    /**
     * @test
     */
    public function testItShouldThrowTypeErrorIfAdapterNotSpecified()
    {
        $this->expectException(\TypeError::class);
        $this->validator = new CheckIfNoDbRecordExists([
            'table' => 'foo',
            'adapter' => Adapter::class,
            'field' => 'bar',
            'exclude' => [
                'field' => 'baz',
                'context_field' => 'baz'
            ]
        ]);
    }

    /**
     * @test
     */
    public function testItShouldSetExcludeValueFromContextIfValueNotSpecified()
    {
        $exclude = [
            'field' => 'baz',
            'context_field' => 'baz',
        ];
        $this->validator = new CheckIfNoDbRecordExists([
            'table' => 'foo',
            'adapter' => $this->adapter,
            'field' => 'bar',
            'exclude' => $exclude
        ]);

        $this->result->shouldReceive('current')->andReturn(true)->once();
        $this->validator->isValid('value', ['baz' => 'bam']);
        $exclude = $this->validator->getExclude();
        $this->assertTrue(isset($exclude['value']), 'value not set');
        $this->assertEquals($exclude['value'], 'bam', 'exclude value not set correctly');
    }
}
