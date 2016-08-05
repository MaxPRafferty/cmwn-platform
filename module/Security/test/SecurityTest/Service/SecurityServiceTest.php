<?php

namespace SecurityTest\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\SecurityUser;
use Security\Service\SecurityService;
use User\Adult;

/**
 * Test SecurityServiceTest
 *
 * @group Security
 * @group User
 * @group Service
 * @group SecurityService
 * @group Authentication
 */
class SecurityServiceTest extends TestCase
{
    /**
     * @var SecurityService
     */
    protected $securityService;

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
        $this->securityService = new SecurityService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldSavePasswordWhenPassedAUser()
    {
        $user = new Adult();
        $user->setUserId('abcdef');

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, $actualWhere) use (&$user) {
                $this->assertArrayHasKey('password', $actualSet);
                $this->assertNotEquals('foobar', $actualSet['password']);

                $this->assertEquals(['user_id' => 'abcdef'], $actualWhere);

                return 1;
            })
            ->once();

        $this->assertTrue($this->securityService->savePasswordToUser($user, 'foobar'));
    }

    /**
     * @test
     */
    public function testItShouldSavePasswordWhenPassedAUserId()
    {
        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, $actualWhere) use (&$user) {
                $this->assertArrayHasKey('password', $actualSet);
                $this->assertNotEquals('foobar', $actualSet['password']);

                $this->assertEquals(['user_id' => 'abcdef'], $actualWhere);

                return 1;
            })
            ->once();

        $this->assertTrue($this->securityService->savePasswordToUser('abcdef', 'foobar'));
    }

    /**
     * @test
     */
    public function testItShouldFetchTheUserByEmail()
    {
        $userData = [
            'password'     => 'hashed',
            'username'     => 'manchuck',
            'code'         => null,
            'code_expires' => 'now',
            'email'        => 'chuck@manchuck.com'
        ];

        $this->tableGateway->shouldReceive('select')
            ->with(['email' => 'chuck@manchuck.com'])
            ->andReturn(new \ArrayIterator([new \ArrayObject($userData)]))
            ->once();

        $this->assertEquals(
            new SecurityUser($userData),
            $this->securityService->fetchUserByEmail('chuck@manchuck.com')
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchTheUserByUserName()
    {
        $userData = [
            'password'     => 'hashed',
            'username'     => 'manchuck',
            'code'         => null,
            'code_expires' => 'now',
            'email'        => 'chuck@manchuck.com'
        ];

        $this->tableGateway->shouldReceive('select')
            ->with(['username' => 'manchuck'])
            ->andReturn(new \ArrayIterator([new \ArrayObject($userData)]))
            ->once();

        $this->assertEquals(
            new SecurityUser($userData),
            $this->securityService->fetchUserByUserName('manchuck')
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNotFoundByUserName()
    {
        $this->setExpectedException(
            'Application\Exception\NotFoundException',
            'User not Found'
        );
        $this->tableGateway->shouldReceive('select')
            ->with(['username' => 'manchuck'])
            ->andReturn(new \ArrayIterator([]))
            ->once();
        $this->tableGateway->shouldReceive('select')
            ->with(['normalized_username' => 'manchuck'])
            ->andReturn(new \ArrayIterator([]))
            ->once();

        $this->securityService->fetchUserByUserName('manchuck');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNotFoundByUserEmail()
    {
        $this->setExpectedException(
            'Application\Exception\NotFoundException',
            'User not Found'
        );

        $this->tableGateway->shouldReceive('select')
            ->with(['email' => 'chuck@manchuck.com'])
            ->andReturn(new \ArrayIterator([]))
            ->once();

        $this->securityService->fetchUserByEmail('chuck@manchuck.com');
    }

    /**
     * @test
     */
    public function testItShouldSaveSuperBitToUser()
    {
        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, $actualWhere) use (&$user) {
                $this->assertArrayHasKey('super', $actualSet, 'Super field is not set');
                $this->assertEquals(1, $actualSet['super'], 'Super bit was not set to 1');

                $this->assertEquals(['user_id' => 'abcdef'], $actualWhere);

                return 1;
            })
            ->once();

        $this->assertTrue($this->securityService->setSuper('abcdef', true));
    }

    /**
     * @test
     */
    public function testItShouldRemoveSuperBitToUser()
    {
        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, $actualWhere) use (&$user) {
                $this->assertArrayHasKey('super', $actualSet, 'Super field is not set');
                $this->assertEquals(0, $actualSet['super'], 'Super bit was not set to 1');

                $this->assertEquals(['user_id' => 'abcdef'], $actualWhere);

                return 1;
            })
            ->once();

        $this->assertTrue($this->securityService->setSuper('abcdef', false));
    }
}
