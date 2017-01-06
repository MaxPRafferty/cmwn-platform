<?php

namespace FriendTest\Service;

use Friend\FriendInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;
use User\UserInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Hydrator\ArraySerializable;

/**
 * Test FriendServiceTest
 *
 * @group FriendService
 * @group Friend
 * @group Service
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FriendServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FriendService
     */
    protected $friendService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var UserInterface
     */
    protected $friend;

    /**
     * @before
     */
    public function setUpGateway()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')
            ->andReturn('user_friends')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->friendService = new FriendService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Child(['user_id' => 'user']);
    }

    /**
     * @before
     */
    public function setUpFriend()
    {
        $this->friend = new Child(['user_id' => 'friend']);
    }

    /**
     * @test
     */
    public function testItShouldReturnDbSelectOnFetchFriends()
    {
        $this->tableGateway->shouldReceive('select')->never();
        $this->assertInstanceOf(
            '\Zend\Paginator\Adapter\DbSelect',
            $this->friendService->fetchFriendsForUser($this->user),
            'friend service did not return paginator adapter'
        );
    }

    /**
     * @test
     */
    public function testItShouldInsertAsPendingIfNotAlreadyInUserFriends()
    {
        $this->tableGateway->shouldReceive('selectWith')->once();
        $this->tableGateway
            ->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actual) {
                $expected = [
                    'user_id'   => 'user',
                    'friend_id' => 'friend',
                    'status'    => FriendInterface::PENDING,
                ];
                $this->assertEquals($expected, $actual);
            });
        $this->assertTrue(
            $this->friendService->attachFriendToUser($this->user, $this->friend)
        );
    }

    /**
     * @test
     */
    public function testItShouldAddFriendIfStatusIsPending()
    {
        $status = [
            'uf_user_id'    => 'friend',
            'uf_friend_id'  => 'user',
            'friend_status' => FriendInterface::PENDING,
        ];
        $result = new ResultSet();
        $result->initialize([$status]);
        $this->tableGateway
            ->shouldReceive('selectWith')
            ->andReturn($result)
            ->once();
        $this->tableGateway
            ->shouldReceive('update')
            ->andReturnUsing(function ($where, $actual) {
                $this->assertEquals(['status' => FriendInterface::FRIEND], $where);
                $expected = [
                    'user_id'   => 'friend',
                    'friend_id' => 'user',
                    'status'    => FriendInterface::PENDING,
                ];
                $this->assertEquals($expected, $actual);
            })->once();
        $this->assertTrue($this->friendService->attachFriendToUser($this->user, $this->friend));
    }

    /**
     * @test
     */
    public function testItShouldDetachFriendFromUserIfFriends()
    {
        $status = [
            'uf_user_id'    => 'user',
            'uf_friend_id'  => 'friend',
            'friend_status' => FriendInterface::FRIEND,
        ];

        $result = new ResultSet();
        $result->initialize([$status]);
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturn($result)->once();
        $where = [
            'user_id'   => $status['uf_user_id'],
            'friend_id' => $status['uf_friend_id'],
            'status'    => $status['friend_status'],
        ];
        $this->tableGateway
            ->shouldReceive('delete')
            ->with($where)
            ->once();
        $this->assertTrue($this->friendService->detachFriendFromUser($this->user, $this->friend));
    }

    /**
     * @test
     */
    public function testItShouldDoNothingIfNotFriends()
    {
        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturn($result)
            ->once();
        $this->tableGateway
            ->shouldReceive('delete')
            ->never();
        $this->assertTrue($this->friendService->detachFriendFromUser($this->user, $this->friend));
    }

    /**
     * @test
     */
    public function testItShouldFetchFriendForUser()
    {
        $result = new ResultSet();
        $row    = [
            'uf_user_id'    => $this->user->getUserId(),
            'friend_status' => FriendInterface::FRIEND,
        ];
        $result->initialize([$row]);
        $this->tableGateway
            ->shouldReceive('selectWith')
            ->andReturn($result);
        $hydrator = new ArraySerializable();
        $this->assertEquals(
            $this->friendService->fetchFriendForUser($this->user, $this->friend),
            $hydrator->hydrate($row, new \ArrayObject())
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFriendsExceptionForUserIfNotFriends()
    {
        $this->setExpectedException(NotFriendsException::class);
        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway
            ->shouldReceive('selectWith')
            ->andReturn($result);
        $this->testItShouldFetchFriendForUser($this->user, $this->friend);
    }

    /**
     * @test
     */
    public function testItShouldFetchFriendStatusForUserFriend()
    {
        $result = new ResultSet();
        $row    = [
            'uf_user_id'    => $this->user->getUserId(),
            'friend_status' => FriendInterface::FRIEND,
        ];
        $result->initialize([$row]);
        $this->tableGateway
            ->shouldReceive('selectWith')
            ->andReturn($result);
        $this->assertEquals(
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            FriendInterface::FRIEND
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchFriendStatusForUserPending()
    {
        $result = new ResultSet();
        $row    = [
            'requesting'    => $this->user->getUserId(),
            'friend_status' => FriendInterface::PENDING,
        ];
        $result->initialize([$row]);
        $this->tableGateway
            ->shouldReceive('selectWith')
            ->andReturn($result);
        $this->assertEquals(
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            FriendInterface::PENDING
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchFriendStatusForUserRequested()
    {
        $result = new ResultSet();
        $row    = [
            'requesting'    => $this->friend->getUserId(),
            'friend_status' => FriendInterface::PENDING,
        ];
        $result->initialize([$row]);
        $this->tableGateway
            ->shouldReceive('selectWith')
            ->andReturn($result);

        $this->assertEquals(
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            FriendInterface::REQUESTED
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchFriendStatusForUser()
    {
        $result = new ResultSet();
        $result->initialize([]);
        $this->setExpectedException(NotFriendsException::class);
        $this->tableGateway
            ->shouldReceive('selectWith')
            ->andReturn($result);
        $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
    }
}
