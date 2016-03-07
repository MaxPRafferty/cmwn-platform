<?php

namespace UserTest\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use User\Service\UserService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Predicate as Where;

/**
 * Test UserServiceTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class UserServiceTest extends TestCase
{
    /**
     * @var UserService
     */
    protected $userService;

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
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('users')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->userService = new UserService($this->tableGateway);
    }

    public function testItShouldReturnPaginatingAdapterByDefaultOnFetchAll()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->never();

        $result = $this->userService->fetchAll(null);
        $this->assertInstanceOf('\Zend\Paginator\Adapter\AdapterInterface', $result);
    }

    public function testItShouldReturnIteratorOnFetchAllWithNoWhereAndNotPaginating()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $where);
                return new \ArrayIterator([]);
            })
            ->once();

        $result = $this->userService->fetchAll(null, false);
        $this->assertInstanceOf('\Iterator', $result);
    }

    public function testItShouldReturnIteratorPassWhereWhenGivenWhereAndNotPaginating()
    {
        $expectedWhere = new Where();
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($where) use (&$expectedWhere) {
                /** @var \Zend\Db\Sql\Predicate\Predicate $where */
                $this->assertSame($expectedWhere, $where);
                return new \ArrayIterator([]);
            })
            ->once();

        $result = $this->userService->fetchAll($expectedWhere, false);
        $this->assertInstanceOf('\Iterator', $result);
    }

    public function testItShouldSaveNewUser()
    {
        $newUser = new Adult();

        $this->assertNull($newUser->getCreated());
        $this->assertNull($newUser->getUpdated());
        $this->assertEmpty($newUser->getUserId());

        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) use (&$newUser) {
                $this->assertNotNull($newUser->getCreated());
                $this->assertNotNull($newUser->getUpdated());
                $this->assertNotEmpty($newUser->getUserId());

                $this->assertTrue(is_array($data));

                $expected = $newUser->getArrayCopy();
                $expected['meta'] = '[]';
                $expected['created'] = $newUser->getCreated()->getTimestamp();
                $expected['updated'] = $newUser->getUpdated()->getTimestamp();
                unset($expected['password']);
                unset($expected['deleted']);


                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals($expected, $data);
                return 1;
            })
            ->once();

        $this->assertTrue($this->userService->createUser($newUser));
    }

    public function testItShouldUpdateExistingUser()
    {
        $userData = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => Adult::GENDER_MALE,
            'birthdate'   => '2016-02-28',
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '2016-02-28',
            'type'        => Adult::TYPE_ADULT
        ];

        $user   = new Adult($userData);
        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['user_id' => $userData['user_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$user) {
                $this->assertEquals(['user_id' => $user->getUserId()], $where);
                $expected = $user->getArrayCopy();
                $expected['meta'] = '[]';

                unset($expected['password']);
                unset($expected['deleted']);

                $expected['updated'] = $user->getUpdated()->getTimestamp();
                $this->assertArrayNotHasKey('deleted', $data);

                $this->assertEquals($expected, $data);

            });

        $this->assertTrue($this->userService->updateUser($user));
    }

    public function testItShouldFetchUserById()
    {
        $userData = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => Adult::GENDER_MALE,
            'birthdate'   => '2016-02-28',
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '2016-02-28',
            'type'        => Adult::TYPE_ADULT
        ];

        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['user_id' => $userData['user_id']])
            ->andReturn($result);

        $this->assertInstanceOf('User\Adult', $this->userService->fetchUser($userData['user_id']));
    }

    public function testItShouldThrowNotFoundExceptionWhenUserIsNotFound()
    {
        $this->setExpectedException(
            'Application\Exception\NotFoundException',
            'User not Found'
        );

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->userService->fetchUser('foo');
    }

    public function testItShouldSoftDeleteByDefault()
    {
        $userData = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => Adult::GENDER_MALE,
            'birthdate'   => '2016-02-28',
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '',
            'type'        => Adult::TYPE_ADULT
        ];

        $user   = new Adult($userData);
        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['user_id' => $userData['user_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$user) {
                $this->assertEquals(['user_id' => $user->getUserId()], $where);
                $this->assertNotEmpty($data['deleted']);
            });

        $this->assertTrue($this->userService->deleteUser($user));
    }

    public function testItShouldSoftDeleteWhenForced()
    {
        $userData = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => Adult::GENDER_MALE,
            'birthdate'   => '2016-02-28',
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '',
            'type'        => Adult::TYPE_ADULT
        ];

        $user   = new Adult($userData);
        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['user_id' => $userData['user_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('delete')
            ->andReturnUsing(function ($where) use (&$user) {
                $this->assertEquals(['user_id' => $user->getUserId()], $where);
            });

        $this->assertTrue($this->userService->deleteUser($user, false));
    }
}
