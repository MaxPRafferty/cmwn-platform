<?php

namespace FeedTest\Service;

use Application\Exception\DuplicateEntryException;
use Application\Exception\NotFoundException;
use Feed\Service\FeedUserService;
use Feed\UserFeed;
use PHPUnit\Framework\TestCase as TestCase;
use User\Child;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FeedUserServiceTest
 * @package FeedTest\Service
 * @SuppressWarnings(PHPMD)
 */
class FeedUserServiceTest extends TestCase
{
    /**
     * @var FeedUserService
     */
    protected $service;

    /**
     * @var TableGateway | \Mockery\MockInterface
     */
    protected $tableGateway;

    /**
     * @var array
     */
    protected $data;

    /**
     * @before
     */
    public function setUpService()
    {
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('user_feed')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();

        $this->service = new FeedUserService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpData()
    {
        $this->data = [
            'feed_id'      => 'es_friend_feed',
            'sender'       => 'english_student',
            'title'        => 'Friendship Made',
            'message'      => 'became friends with',
            'priority'     => 5,
            'posted'       => '2016-04-15 11:49:08',
            'visibility'   => 2,
            'type'         => 'FRIEND',
            'type_version' => 1,
            'created'      => null,
            'updated'      => null,
            'deleted'      => null,
            'read_flag'    => 0,
        ];
    }

    /**
     * @test
     */
    public function testItShouldFetchAllUserFeed()
    {
        $select = new Select(['uf' => $this->tableGateway->getTable()]);
        $where = new Where();
        $where->addPredicate(new PredicateSet(
            [
                new Expression('f.visibility = 0'),
                new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
            ],
            PredicateSet::COMBINED_BY_OR
        ));

        $select->join(
            ['f' => 'feed'],
            'uf.feed_id = f.feed_id',
            '*',
            Select::JOIN_RIGHT_OUTER
        );
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->columns(['read_flag']);
        $select->where($where);
        $select->order('f.priority DESC');
        $dbSelect = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            new HydratingResultSet(new ArraySerializable(), new UserFeed())
        );

        $this->assertEquals(
            $dbSelect,
            $this->service->fetchAllFeedForUser(new Child(['user_id' => 'english_student']))
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchFeedForUser()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);

                $this->assertEquals($expectedSelect, $select);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })
            ->once();

        $this->service->fetchFeedForUser('english_student', 'es_friend_feed');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFeedNotFound()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);
                $resultSet = new ResultSet();
                $resultSet->initialize([]);
                return $resultSet;
            })
            ->once();
        $this->expectException(NotFoundException::class);

        $this->assertInstanceOf(
            NotFoundException::class,
            $this->service->fetchFeedForUser('english_student', 'es_friend_feed')
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachFeedForUser()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);

                $this->assertEquals($expectedSelect, $select);
                $resultSet = new ResultSet();
                $resultSet->initialize([]);
                return $resultSet;
            })
            ->once();

        $this->tableGateway->shouldReceive('insert')
            ->with([
                'user_id' => 'english_student',
                'feed_id' => 'es_friend_feed',
                'read_flag' => 0
            ])
            ->once();
        $this->service->attachFeedForUser('english_student', new UserFeed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldNotAttachFeedWhenUserFeedAlreadyExists()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);

                $this->assertEquals($expectedSelect, $select);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })
            ->once();

        $this->expectException(DuplicateEntryException::class);

        $this->tableGateway->shouldReceive('insert')
            ->never();
        $this->service->attachFeedForUser('english_student', new UserFeed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldUpdateTheReadStatus()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);

                $this->assertEquals($expectedSelect, $select);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })
            ->once();

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) {
                $this->assertEquals(['read_flag' => 0], $data);
                $this->assertEquals(['user_id' => 'english_student', 'feed_id' => 'es_friend_feed'], $where);
            })->once();

        $this->assertTrue($this->service->updateFeedForUser('english_student', new UserFeed($this->data)));
    }

    /**
     * @test
     */
    public function testItShouldNotUpdateWhenFeedNotFound()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);

                $this->assertEquals($expectedSelect, $select);
                $resultSet = new ResultSet();
                $resultSet->initialize([]);
                return $resultSet;
            })
            ->once();

        $this->expectException(NotFoundException::class);

        $this->service->updateFeedForUser('english_student', new UserFeed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldDeleteFeedForUser()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);

                $this->assertEquals($expectedSelect, $select);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })
            ->once();

        $this->tableGateway->shouldReceive('delete')
            ->with(['user_id' => 'english_student', 'feed_id' => 'es_friend_feed'])
            ->once();

        $this->service->deleteFeedForUser('english_student', new UserFeed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldNotDeleteWhenFeedNotFound()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturnUsing(function ($select) {
                $where = new Where();
                $where->addPredicate(new Operator('uf.feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $where->addPredicate(new PredicateSet(
                    [
                        new Expression('f.visibility = 0'),
                        new Operator('uf.user_id', Operator::OP_EQ, 'english_student')
                    ],
                    PredicateSet::COMBINED_BY_OR
                ));

                $expectedSelect = new Select(['uf' => $this->tableGateway->getTable()]);
                $expectedSelect->columns(['read_flag']);
                $expectedSelect->join(
                    ['f' => 'feed'],
                    'uf.feed_id = f.feed_id',
                    '*',
                    Select::JOIN_RIGHT_OUTER
                );
                $expectedSelect->where($where);

                $this->assertEquals($expectedSelect, $select);
                $resultSet = new ResultSet();
                $resultSet->initialize([]);
                return $resultSet;
            })
            ->once();

        $this->expectException(NotFoundException::class);

        $this->tableGateway->shouldReceive('delete')->never();

        $this->service->deleteFeedForUser('english_student', new UserFeed($this->data));
    }
}
