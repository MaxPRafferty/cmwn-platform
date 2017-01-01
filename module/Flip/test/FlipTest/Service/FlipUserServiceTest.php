<?php

namespace FlipTest\Service;

use Flip\EarnedFlip;
use Flip\Service\FlipUserService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Ramsey\Uuid\Uuid;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test FlipUserServiceTest
 *
 * @group Flip
 * @group User
 * @group Service
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipUserServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FlipUserService(
     */
    protected $flipService;

    /**
     * @var \Mockery\MockInterface|TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|Adapter $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('user_flips')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->flipService = new FlipUserService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlipsForUserWithDefaultValuesAndStringForUserId()
    {
        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), new EarnedFlip());
        $expectedSelect    = new Select(['f' => 'flips']);
        $where             = new Where();

        $where->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $expectedSelect->join(
            ['uf' => 'user_flips'],
            new Expression('uf.user_id = ?', 'foo-bar'),
            ['earned' => 'earned', 'user_id' => 'earned_by'],
            Select::JOIN_LEFT
        );

        $expectedSelect->where($where);
        $expectedSelect->group('f.flip_id');
        $expectedSelect->order(['uf.earned', 'f.title']);
        $expectedAdapter = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchEarnedFlipsForUser('foo-bar'),
            FlipUserService::class . ' did not return correct paginator adapter using a string for the user id'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlipsForUserWithCustomProtoTypeAndWhere()
    {
        /** @var \Mockery\MockInterface|\Flip\EarnedFlipInterface $prototype */
        $prototype = \Mockery::mock('\Flip\EarnedFlipInterface');
        $where     = new Where();
        $where->addPredicate(new Operator('foo', '=', 'bar'));

        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $expectedSelect    = new Select(['f' => 'flips']);
        $expectedWhere     = new Where();
        $expectedSelect->group('f.flip_id');
        $expectedSelect->order(['uf.earned', 'f.title']);

        $expectedWhere->addPredicate(new Operator('foo', '=', 'bar'));
        $expectedWhere->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $expectedSelect->join(
            ['uf' => 'user_flips'],
            new Expression('uf.user_id = ?', 'foo-bar'),
            ['earned' => 'earned', 'user_id' => 'earned_by'],
            Select::JOIN_LEFT
        );

        $expectedSelect->where($expectedWhere);

        $expectedAdapter = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchEarnedFlipsForUser('foo-bar', $where, $prototype),
            FlipUserService::class . ' did not return correct paginator adapter using a custom prototype'
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachFlipToUser()
    {
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actualInsert) {
                $this->assertEquals(
                    ['user_id', 'flip_id', 'earned', 'acknowledge_id'],
                    array_keys($actualInsert),
                    FlipUserService::class . ' is setting the wrong fields'
                );

                $this->assertArrayHasKey(
                    'earned',
                    $actualInsert,
                    FlipUserService::class . ' did not include the earned date on attach'
                );

                $this->assertArrayHasKey(
                    'acknowledge_id',
                    $actualInsert,
                    FlipUserService::class . ' did not include the acknowledge id on attach'
                );

                $this->assertEquals(
                    'foo-bar',
                    $actualInsert['user_id'],
                    FlipUserService::class . ' service did not use correct user Id'
                );

                $this->assertEquals(
                    'baz-bat',
                    $actualInsert['flip_id'],
                    FlipUserService::class . ' did not use correct flip Id'
                );

                $this->assertNotInstanceOf(
                    \DateTime::class,
                    $actualInsert['earned'],
                    FlipUserService::class . ' did not set the earned date correctly'
                );

                return true;
            });

        $this->flipService->attachFlipToUser('foo-bar', 'baz-bat');
    }

    /**
     * @test
     */
    public function testItShouldAcknowledgeFlip()
    {
        $ackId      = Uuid::uuid1();
        $earnedFlip = new EarnedFlip();
        $earnedFlip->setAcknowledgeId($ackId);
        $this->tableGateway->shouldReceive('update')
            ->with(['acknowledge_id' => null], ['acknowledge_id' => $ackId])
            ->andReturn(1)
            ->once();

        $this->assertTrue(
            $this->flipService->acknowledgeFlip($earnedFlip),
            FlipUserService::class . ' did not acknowledge earned flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotAcknowledgeFlip()
    {
        $ackId      = Uuid::uuid1();
        $earnedFlip = new EarnedFlip();
        $earnedFlip->setAcknowledgeId($ackId);
        $this->tableGateway->shouldReceive('update')
            ->with(['acknowledge_id' => null], ['acknowledge_id' => $ackId])
            ->andReturn(0)
            ->once();

        $this->assertFalse(
            $this->flipService->acknowledgeFlip($earnedFlip),
            FlipUserService::class . ' did acknowledged earned flip'
        );
    }
}
