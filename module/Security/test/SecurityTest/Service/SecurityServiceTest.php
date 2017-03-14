<?php

namespace SecurityTest\Service;

use Application\Exception\NotFoundException;
use Lcobucci\JWT\Builder;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Security\SecurityUser;
use Security\Service\SecurityService;
use User\Adult;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * Test SecurityServiceTest
 *
 * @group Security
 * @group User
 * @group Service
 * @group SecurityService
 * @group Authentication
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SecurityServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
    public function setUpService()
    {
        $this->securityService = new SecurityService($this->tableGateway);
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
            'email'        => 'chuck@manchuck.com',
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
            'email'        => 'chuck@manchuck.com',
        ];

        $this->tableGateway->shouldReceive('select')
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
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not Found');

        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($predicateSet) {
                $this->assertInstanceOf(PredicateSet::class, $predicateSet);
                return new \ArrayIterator([]);
            })->once();

        $this->securityService->fetchUserByUserName('manchuck');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNotFoundByUserEmail()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not Found');

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

    /**
     * @test
     * @ticket CORE-1162
     * @ticket CORE-2713
     */
    public function testItShouldSetTheCodeToExpireInOneDayByDefault()
    {
        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($set, $where) {
                $this->assertTrue(is_array($set));
                $this->assertArrayHasKey('code', $set);
                $this->assertArrayNotHasKey('code_expires', $set);

                $start = new \DateTime('now');
                $start->setTime(00, 00, 00);

                $expires = clone $start;
                $expires->add(new \DateInterval('P1D'));
                $expires->setTime(23, 59, 59);

                $token     = (new Builder())->setAudience('student')
                    ->setIssuedAt(time())
                    ->setNotBefore($start->getTimestamp())
                    ->setExpiration($expires->getTimestamp())
                    ->setId('foobar')
                    ->getToken();

                $this->assertEquals(
                    $set['code'],
                    $token->__toString(),
                    'Invalid JWT token set'
                );

                $this->assertEquals(['user_id' => 'student'], $where);
            })
            ->once();
        $this->assertTrue($this->securityService->saveCodeToUser('foobar', 'student'));
    }

    /**
     * @test
     * @ticket CORE-1162
     * @ticket CORE-2713
     */
    public function testItShouldSetTheCodeToExpireInThirtyDays()
    {
        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($set, $where) {
                $this->assertTrue(is_array($set));
                $this->assertArrayHasKey('code', $set);
                $this->assertArrayNotHasKey('code_expires', $set);

                $start = new \DateTime('now');
                $start->setTime(00, 00, 00);

                $expires = clone $start;
                $expires->add(new \DateInterval('P30D'));
                $expires->setTime(23, 59, 59);

                $token     = (new Builder())->setAudience('student')
                    ->setIssuedAt(time())
                    ->setNotBefore($start->getTimestamp())
                    ->setExpiration($expires->getTimestamp())
                    ->setId('foobar')
                    ->getToken();

                $this->assertEquals(
                    $set['code'],
                    $token->__toString(),
                    'Invalid JWT token set'
                );

                $this->assertEquals(['user_id' => 'student'], $where);
            })
            ->once();
        $this->assertTrue($this->securityService->saveCodeToUser('foobar', 'student', 30));
    }

    /**
     * @test
     * @ticket CORE-1162
     */
    public function testItShouldSetTheCodeToExpireInWithPositiveDays()
    {
        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($set, $where) {
                $this->assertTrue(is_array($set));
                $this->assertArrayHasKey('code', $set);
                $this->assertArrayNotHasKey('code_expires', $set);

                $start = new \DateTime('now');
                $start->setTime(00, 00, 00);

                $expires = clone $start;
                $expires->add(new \DateInterval('P15D'));
                $expires->setTime(23, 59, 59);

                $token     = (new Builder())->setAudience('student')
                    ->setIssuedAt(time())
                    ->setNotBefore($start->getTimestamp())
                    ->setExpiration($expires->getTimestamp())
                    ->setId('foobar')
                    ->getToken();

                $this->assertEquals(
                    $set['code'],
                    $token->__toString(),
                    'Invalid JWT token set'
                );

                $this->assertEquals(['user_id' => 'student'], $where);
            })
            ->once();
        $this->assertTrue($this->securityService->saveCodeToUser('foobar', 'student', -15));
    }

    /**
     * @test
     * @ticket CORE-2713
     */
    public function testItShouldSetTheCodeToExpireFromStartDate()
    {
        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($set, $where) {
                $this->assertTrue(is_array($set));
                $this->assertArrayHasKey('code', $set);
                $this->assertArrayNotHasKey('code_expires', $set);

                $start = new \DateTime('tomorrow');
                $start->setTime(00, 00, 00);

                $expires = clone $start;
                $expires->add(new \DateInterval('P5D'));
                $expires->setTime(23, 59, 59);

                $token     = (new Builder())->setAudience('student')
                    ->setIssuedAt(time())
                    ->setNotBefore($start->getTimestamp())
                    ->setExpiration($expires->getTimestamp())
                    ->setId('foobar')
                    ->getToken();

                $this->assertEquals(
                    $set['code'],
                    $token->__toString(),
                    'Invalid JWT token set'
                );

                $this->assertEquals(['user_id' => 'student'], $where);
            })
            ->once();
        $this->assertTrue($this->securityService->saveCodeToUser('foobar', 'student', 5, new \DateTime('tomorrow')));
    }

    /**
     * @test
     */
    public function testItShouldResetCodeForGroup()
    {
        $resultSet = new ResultSet();
        $resultSet->initialize([['user_id' => 'english_student'], ['user_id' => 'math_student']]);
        $this->tableGateway->shouldReceive('selectWith')
            ->andReturn($resultSet)->once();
        $this->tableGateway->shouldReceive('update')->twice();
        $this->assertTrue($this->securityService->saveCodeToGroup('foobar', 'school'));
    }
}
