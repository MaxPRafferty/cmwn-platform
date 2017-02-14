<?php

namespace FeedTest\Service;

use Application\Exception\NotFoundException;
use Feed\Feed;
use Feed\Service\FeedService;
use PHPUnit\Framework\TestCase as TestCase;
use User\Child;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FeedServiceTest
 * @package FeedTest\Service
 * @SuppressWarnings(PHPMD)
 */
class FeedServiceTest extends TestCase
{
    /**
     * @var FeedService
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
        $this->tableGateway->shouldReceive('getTable')->andReturn('feed')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();

        $this->service = new FeedService($this->tableGateway);
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
        ];
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFeed()
    {
        $select = new Select(['ft' => $this->tableGateway->getTable()]);
        $where = new Where();
        $where->isNull('ft.deleted');
        $select->where($where);
        $select->order('priority DESC');
        $dbSelect = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            new HydratingResultSet(new ArraySerializable(), new Feed())
        );
        $this->assertEquals($dbSelect, $this->service->fetchAll());
    }

    /**
     * @test
     */
    public function testItShouldFetchFeedById()
    {
        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $expected = new Where();
                $expected->addPredicate(new Operator('feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $this->assertEquals($expected, $where);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })->once();
        $this->service->fetchFeed('es_friend_feed');
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenFeedNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $expected = new Where();
                $expected->addPredicate(new Operator('feed_id', Operator::OP_EQ, 'friend_feed'));
                $this->assertEquals($expected, $where);
                $resultSet = new ResultSet();
                $resultSet->initialize([]);
                return $resultSet;
            });
        $this->assertInstanceOf(NotFoundException::class, $this->service->fetchFeed('friend_feed'));
    }

    /**
     * @test
     */
    public function testItShouldCreateFeedWithSenderAsString()
    {
        $data = $this->data;
        unset($data['feed_id']);
        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) {
                $this->assertNotNull($data['feed_id']);
                $this->assertNotNull($data['created']);
                $this->assertNotNull($data['updated']);
                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals('english_student', $data['sender']);
                return true;
            })
            ->once();
        $this->service->createFeed(new Feed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldCreateFeedWithSenderAsUserObject()
    {
        $data = $this->data;
        $data['sender'] = new Child(['user_id' => $this->data['sender']]);
        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) {
                $this->assertNotNull($data['feed_id']);
                $this->assertNotNull($data['created']);
                $this->assertNotNull($data['updated']);
                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals('english_student', $data['sender']);
                return true;
            })
            ->once();
        $this->service->createFeed(new Feed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldUpdateFeed()
    {
        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $expected = new Where();
                $expected->addPredicate(new Operator('feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $this->assertEquals($expected, $where);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })->once();

        $feed = new Feed($this->data);
        $updated = new \DateTime();
        $updated->format('Y-m-d H-i-s');
        $feed->setUpdated($updated);
        $this->tableGateway->shouldReceive('update')
            ->once();
        $this->service->updateFeed(new Feed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldNotUpdateWhenFeedNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $expected = new Where();
                $expected->addPredicate(new Operator('feed_id', Operator::OP_EQ, 'friend_feed'));
                $this->assertEquals($expected, $where);
                $resultSet = new ResultSet();
                $resultSet->initialize([]);
                return $resultSet;
            });
        $this->assertInstanceOf(NotFoundException::class, $this->service->fetchFeed('friend_feed'));

        $this->tableGateway->shouldReceive('update')->never();
        $this->service->updateFeed(new Feed($this->data));
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteFeed()
    {
        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $expected = new Where();
                $expected->addPredicate(new Operator('feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $this->assertEquals($expected, $where);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })->once();

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) {
                $this->assertEquals(['deleted' => (new \DateTime())->format('Y-m-d H-i-s')], $data);
                $this->assertEquals(['feed_id' => 'es_friend_feed'], $where);
            })->once();
        $this->assertTrue($this->service->deleteFeed(new Feed($this->data)));
    }

    /**
     * @test
     */
    public function testItShouldDeleteFeed()
    {
        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $expected = new Where();
                $expected->addPredicate(new Operator('feed_id', Operator::OP_EQ, 'es_friend_feed'));
                $this->assertEquals($expected, $where);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->data]);
                return $resultSet;
            })->once();

        $this->tableGateway->shouldReceive('delete')
            ->andReturnUsing(function ($where) {
                $this->assertEquals(['feed_id' => 'es_friend_feed'], $where);
            })->once();
        $this->assertTrue($this->service->deleteFeed(new Feed($this->data), false));
    }
}
