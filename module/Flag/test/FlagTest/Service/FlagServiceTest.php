<?php

namespace FlagTest;

use Application\Exception\NotFoundException;
use Flag\Flag;
use Flag\FlagInterface;
use Flag\Service\FlagService;
use Flag\Service\FlagServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FlagServiceTest
 *
 * @package FlagTest
 * @group   Flag
 * @group   Service
 * @group   FlagService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlagServiceTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface | \Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var \Mockery\MockInterface | \Flag\FlagHydrator
     */
    protected $flagHydrator;

    /**
     * @var FlagServiceInterface
     */
    protected $flagService;

    /**
     * @var Flag
     */
    protected $flag;

    /**
     * @before
     */
    public function setUpTableGateway()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('image_flags')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpHydrator()
    {
        $this->flagHydrator = \Mockery::mock(\Flag\FlagHydrator::class);
    }

    /**
     * @before
     */
    public function setUpFlagService()
    {
        $this->flagService = new FlagService($this->tableGateway, $this->flagHydrator);
    }

    /**
     * @before
     */
    public function setUpFlag()
    {
        $flagData   = [
            'flag_id' => 'foo',
            'flagger' => new Child(['user_id' => 'english_student', 'username' => 'flagger']),
            'flaggee' => new Child(['user_id' => 'english_student', 'username' => 'flaggee']),
            'url'     => '/foo',
            'reason'  => 'bar',
        ];
        $this->flag = new Flag($flagData);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlaggedImages()
    {
        $resultSet = new HydratingResultSet($this->flagHydrator, new Flag());
        $this->flagHydrator
            ->shouldReceive('setPrototype')
            ->once();
        $expected = new DbSelect(
            new Select(['ft' => $this->tableGateway->getTable()]),
            $this->tableGateway->getAdapter(),
            $resultSet
        );
        $actual   = $this->flagService->fetchAll();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testItShouldFetchFlagById()
    {
        $this->flagHydrator
            ->shouldReceive('setPrototype')
            ->once();
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($actual) {
                $where = new Where();
                $where->addPredicate(new Operator('flag_id', Operator::OP_EQ, 'foo'));
                $this->assertEquals(
                    $where,
                    $actual,
                    'Flag Service is not fetching flags correctly'
                );
                $resultSet           = new ResultSet();
                $flagData            = $this->flag->getArrayCopy();
                $flagData['flagger'] = $flagData['flagger']['user_id'];
                $flagData['flaggee'] = $flagData['flaggee']['user_id'];
                $resultSet->initialize([$flagData]);

                return $resultSet;
            });
        $this->flagHydrator
            ->shouldReceive('hydrate')
            ->once();
        $this->assertInstanceOf(FlagInterface::class, $this->flagService->fetchFlag('foo'));
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFlagNotFound()
    {
        $resultSet = new ResultSet();
        $resultSet->initialize([]);
        $this->flagHydrator
            ->shouldReceive('setPrototype')
            ->once();
        $this->setExpectedException(NotFoundException::class);
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturn($resultSet)
            ->once();
        $this->flagHydrator->shouldReceive('hydrate')->never();
        $this->flagService->fetchFlag('foo');
    }

    /**
     * @test
     */
    public function testItShouldSaveFlag()
    {
        $this->tableGateway
            ->shouldReceive('insert')
            ->once();
        $this->assertTrue($this->flagService->saveFlag($this->flag));
    }

    /**
     * @test
     */
    public function testItShouldUpdateFlag()
    {
        $this->tableGateway
            ->shouldReceive('update')
            ->once();
        $this->assertTrue($this->flagService->updateFlag($this->flag));
    }

    /**
     * @test
     */
    public function testItShouldDeleteFlag()
    {
        $resultSet           = new ResultSet();
        $flagData            = $this->flag->getArrayCopy();
        $flagData['flagger'] = $flagData['flagger']['user_id'];
        $flagData['flaggee'] = $flagData['flaggee']['user_id'];
        $resultSet->initialize([$flagData]);

        $this->flagHydrator
            ->shouldReceive('setPrototype')
            ->once();
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturn($resultSet);
        $this->flagHydrator
            ->shouldReceive('hydrate')
            ->once();
        $this->tableGateway
            ->shouldReceive('delete')
            ->once();
        $this->assertTrue($this->flagService->deleteFlag($this->flag));
    }

    /**
     * @test
     */
    public function testItShouldThrowErrorWhileDeletingNonExistentFlag()
    {
        $resultSet = new ResultSet();
        $resultSet->initialize([]);
        $this->flagHydrator
            ->shouldReceive('setPrototype')
            ->once();
        $this->setExpectedException(NotFoundException::class);
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturn($resultSet)
            ->once();
        $this->tableGateway
            ->shouldReceive('delete')
            ->never();
        $this->assertTrue($this->flagService->deleteFlag($this->flag));
    }
}
