<?php

namespace SecurityTest\Rule\Rule;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use Security\Authorization\Rbac;
use Security\Rule\Rule\HasPermission;
use Security\SecurityUserInterface;

/**
 * Test HasPermissionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HasPermissionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|Rbac
     */
    protected $rbac;

    /**
     * @before
     */
    public function setUpRbac()
    {
        $this->rbac = \Mockery::mock(Rbac::class);
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenRoleHasPermission()
    {
        $item = new BasicRuleItem(new BasicValueProvider('active_role', 'my_role'));
        $this->rbac->shouldReceive('isGranted')
            ->with('my_role', 'foo')
            ->andReturn(true);

        $rule = new HasPermission($this->rbac, 'foo');
        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            HasPermission::class . ' is unhappy but should be glad, the role has permission'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            HasPermission::class . ' is reporting the incorrect times its been happy'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeHappyWhenTheRoleDoesNotHaveThePermission()
    {
        $item = new BasicRuleItem(new BasicValueProvider('active_role', 'my_role'));
        $this->rbac->shouldReceive('isGranted')
            ->with('my_role', 'foo')
            ->andReturn(false);

        $rule = new HasPermission($this->rbac, 'foo');
        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            HasPermission::class . ' is happy but this role should make it sad'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            HasPermission::class . ' is reporting the incorrect times its been happy'
        );
    }

    /**
     * @test
     */
    public function testItShouldUseGuestRoleWhenProviderDidNotProvideRole()
    {
        $item = new BasicRuleItem(new BasicValueProvider('active_role', 'my_role'));
        $this->rbac->shouldReceive('isGranted')
            ->with(SecurityUserInterface::ROLE_GUEST, 'foo')
            ->andReturn(false);

        $rule = new HasPermission($this->rbac, 'foo', 'not_in_provider');
        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            HasPermission::class . ' is happy but this role should make it sad'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            HasPermission::class . ' is reporting the incorrect times its been happy'
        );
    }
}
