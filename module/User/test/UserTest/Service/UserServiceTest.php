<?php

namespace UserTest\Service;

use Application\Exception\NotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\Adult;
use User\Service\UserService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Test UserServiceTest
 *
 * @group User
 * @group Service
 * @group UserService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
    public function setUpService()
    {
        $this->userService = new UserService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('users')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatingAdapterByDefaultOnFetchAll()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->never();

        $result = $this->userService->fetchAll(null);
        $this->assertInstanceOf(AdapterInterface::class, $result);
    }

    /**
     * @test
     */
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

                $expected                        = $newUser->getArrayCopy();
                $expected['meta']                = '[]';
                $expected['created']             = $newUser->getCreated()->format("Y-m-d H:i:s");
                $expected['updated']             = $newUser->getUpdated()->format("Y-m-d H:i:s");
                $expected['normalized_username'] = '';
                unset($expected['password']);
                unset($expected['deleted']);

                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals($expected, $data);

                return 1;
            })
            ->once();

        $this->assertTrue($this->userService->createUser($newUser));
    }

    /**
     * @test
     * @ticket CORE-645
     */
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
            'type'        => Adult::TYPE_ADULT,
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
                $expected         = $user->getArrayCopy();
                $expected['meta'] = '[]';

                unset($expected['password']);
                unset($expected['deleted']);
                unset($expected['type']);
                unset($expected['username']);
                $expected['normalized_username'] = 'manchuck';

                $expected['updated'] = $user->getUpdated()->format("Y-m-d H:i:s");
                $this->assertArrayNotHasKey('deleted', $data);

                $this->assertEquals($expected, $data);
            });

        $this->assertTrue($this->userService->updateUser($user));
    }

    /**
     * @test
     */
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
            'type'        => Adult::TYPE_ADULT,
        ];

        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['user_id' => $userData['user_id']])
            ->andReturn($result);

        $this->assertInstanceOf(Adult::class, $this->userService->fetchUser($userData['user_id'], null));
    }

    /**
     * @test
     */
    public function testItShouldFetchUserByExternalId()
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
            'type'        => Adult::TYPE_ADULT,
            'external_id' => 'foo-bar',
        ];

        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['external_id' => $userData['external_id']])
            ->andReturn($result);

        $this->assertInstanceOf(Adult::class, $this->userService->fetchUserByExternalId($userData['external_id']));
    }

    /**
     * @test
     */
    public function testItShouldFetchUserByEmail()
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
            'type'        => Adult::TYPE_ADULT,
            'external_id' => 'foo-bar',
        ];

        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['email' => $userData['email']])
            ->andReturn($result);

        $this->assertInstanceOf(Adult::class, $this->userService->fetchUserByEmail($userData['email']));
    }

    /**
     * @test
     */
    public function testItShouldFetchUserByUsername()
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
            'type'        => Adult::TYPE_ADULT,
            'external_id' => 'foo-bar',
        ];

        $result = new ResultSet();
        $result->initialize([$userData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['username' => $userData['username']])
            ->andReturn($result);

        $this->assertInstanceOf(Adult::class, $this->userService->fetchUserByUsername($userData['username']));
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenUserIsNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not Found');

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->userService->fetchUser('foo', null);
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenUserIsNotFoundByExternalId()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not Found');

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->userService->fetchUserByExternalId('foo');
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenUserIsNotFoundByEmail()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not Found');

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->userService->fetchUserByEmail('foo@example.com');
    }

    /**
     * @test
     */
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
            'type'        => Adult::TYPE_ADULT,
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

    /**
     * @test
     */
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
            'type'        => Adult::TYPE_ADULT,
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
