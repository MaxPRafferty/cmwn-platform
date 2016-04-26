<?php

namespace FlipTest\Service;

use Flip\EarnedFlip;
use Flip\Service\FlipUserService;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test FlipUserServiceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlipUserServiceTest extends TestCase
{
    /**
     * @var FlipUserService(
     */
    protected $flipService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock(\Zend\Db\Adapter\Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(\Zend\Db\TableGateway\TableGateway::class);
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
            ['earned' => 'earned'],
            Select::JOIN_LEFT
        );

        $expectedSelect->where($where);

        $expectedAdapter   = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchEarnedFlipsForUser('foo-bar'),
            'Flip User Service did not return correct adapter'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlipsForUserWithCustomProtoTypeAndWhere()
    {
        /** @var \Mockery\MockInterface|\Flip\EarnedFlipInterface $prototype */
        $prototype = \Mockery::mock('\Flip\EarnedFlipInterface');
        $where   = new Where();
        $where->addPredicate(new Operator('foo', '=', 'bar'));

        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $expectedSelect    = new Select(['f' => 'flips']);
        $expectedWhere     = new Where();

        $expectedWhere->addPredicate(new Operator('foo', '=', 'bar'));
        $expectedWhere->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $expectedSelect->join(
            ['uf' => 'user_flips'],
            new Expression('uf.user_id = ?', 'foo-bar'),
            ['earned' => 'earned'],
            Select::JOIN_LEFT
        );

        $expectedSelect->where($expectedWhere);

        $expectedAdapter   = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchEarnedFlipsForUser('foo-bar', $where, $prototype),
            'Flip User Service did not return correct adapter'
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
                $this->assertTrue(
                    is_array($actualInsert),
                    'User flip service called insert with non array'
                );

                $this->assertArrayHasKey(
                    'user_id',
                    $actualInsert,
                    'User flip service did not include the user id on insert'
                );

                $this->assertArrayHasKey(
                    'flip_id',
                    $actualInsert,
                    'User flip service did not include the flip id on insert'
                );

                $this->assertArrayHasKey(
                    'earned',
                    $actualInsert,
                    'User flip service did not include the earned id on insert'
                );

                $this->assertEquals(
                    'foo-bar',
                    $actualInsert['user_id'],
                    'Flip user service did not use correct user Id'
                );

                $this->assertEquals(
                    'baz-bat',
                    $actualInsert['flip_id'],
                    'Flip user service did not use correct flip Id'
                );

                $this->assertNotInstanceOf(
                    \DateTime::class,
                    $actualInsert['earned'],
                    'Flip User Service did not set the earned date correctly'
                );

                return true;
            });

        $this->flipService->attachFlipToUser('foo-bar', 'baz-bat');
    }
}
