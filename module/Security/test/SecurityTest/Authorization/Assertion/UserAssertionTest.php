<?php

namespace SecurityTest\Authorization\Assertion;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Authorization\Assertion\UserAssertion;
use Security\Authorization\Rbac;
use Security\Service\SecurityGroupServiceInterface;
use User\Adult;
use User\Child;

/**
 * Test UserAssertionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserAssertionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|SecurityGroupServiceInterface
     */
    protected $securityGroup;

    /**
     * @var \Mockery\MockInterface|Rbac
     */
    protected $rbac;

    /**
     * @var UserAssertion
     */
    protected $assertion;

    /**
     * @before
     */
    public function setUpSecurityGroupService()
    {
        $this->securityGroup = \Mockery::mock(SecurityGroupServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpRbac()
    {
        $this->rbac = \Mockery::mock(Rbac::class);
    }

    /**
     * @before
     */
    public function setUpAssertion()
    {
        $this->assertion = new UserAssertion($this->securityGroup);
    }

    /**
     * @test
     */
    public function testItShouldAssertNotAllowedWhenMissingRequestedUser()
    {
        $this->rbac->shouldNotReceive('isGranted');

        $this->assertFalse(
            $this->assertion->assert($this->rbac),
            UserAssertion::class . ' did not assert false with no requested user'
        );
    }

    /**
     * @test
     */
    public function testItShouldCheckMeAdultRoleWhenActiveAndRequestedAreTheSame()
    {
        $user = new Adult(['user_id' => 'foobar']);
        $this->assertion->setActiveUser($user);
        $this->assertion->setRequestedUser($user);

        $this->rbac->shouldReceive('isGranted')
            ->with('me.adult', 'foo')
            ->andReturn(false);

        $this->rbac->shouldReceive('isGranted')
            ->with('me.adult', 'bar')
            ->andReturn(true);

        $this->assertion->setPermission(['foo', 'bar']);

        $this->assertTrue(
            $this->assertion->assert($this->rbac),
            UserAssertion::class . ' did not allow me.adult access to permission bar'
        );
    }

    /**
     * @test
     */
    public function testItShouldCheckMeChildRoleWhenActiveAndRequestedAreTheSame()
    {
        $user = new Child(['user_id' => 'foobar']);
        $this->assertion->setActiveUser($user);
        $this->assertion->setRequestedUser($user);

        $this->rbac->shouldReceive('isGranted')
            ->with('me.child', 'foo')
            ->andReturn(false);

        $this->rbac->shouldReceive('isGranted')
            ->with('me.child', 'bar')
            ->andReturn(true);

        $this->assertion->setPermission(['foo', 'bar']);

        $this->assertTrue(
            $this->assertion->assert($this->rbac),
            UserAssertion::class . ' did not allow me.adult access to permission bar'
        );
    }

    /**
     * @test
     */
    public function testItShouldFindRoleBetweenTwoUsers()
    {
        $activeUser    = new Child(['user_id' => 'foo-bar']);
        $requestedUser = new Adult(['user_id' => 'baz-bat']);
        $this->assertion->setActiveUser($activeUser);
        $this->assertion->setRequestedUser($requestedUser);

        $this->securityGroup->shouldReceive('fetchRelationshipRole')
            ->with($activeUser, $requestedUser)
            ->andReturn('relates');

        $this->rbac->shouldReceive('isGranted')
            ->with('relates', 'foo')
            ->andReturn(false);

        $this->rbac->shouldReceive('isGranted')
            ->with('relates', 'bar')
            ->andReturn(true);

        $this->assertion->setPermission(['foo', 'bar']);

        $this->assertTrue(
            $this->assertion->assert($this->rbac),
            UserAssertion::class . ' did not allow relates access to permission bar'
        );
    }

    /**
     * @test
     */
    public function testItShouldFailWhenRoleIsNotAllowed()
    {
        $activeUser    = new Child(['user_id' => 'foo-bar']);
        $requestedUser = new Adult(['user_id' => 'baz-bat']);
        $this->assertion->setActiveUser($activeUser);
        $this->assertion->setRequestedUser($requestedUser);

        $this->securityGroup->shouldReceive('fetchRelationshipRole')
            ->with($activeUser, $requestedUser)
            ->andReturn('relates');

        $this->rbac->shouldReceive('isGranted')
            ->with('relates', 'foo')
            ->andReturn(false);

        $this->rbac->shouldReceive('isGranted')
            ->with('relates', 'bar')
            ->andReturn(false);

        $this->assertion->setPermission(['foo', 'bar']);

        $this->assertFalse(
            $this->assertion->assert($this->rbac),
            UserAssertion::class . ' did allowed relates access to foo or bar'
        );
    }
}
