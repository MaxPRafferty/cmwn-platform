<?php

namespace SecurityTest\Authorization\Assertion;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Authorization\Assertion\DefaultAssertion;
use Security\Authorization\Rbac;

/**
 * Test DefaultAssertionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultAssertionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function testItShouldCheckAllPermissionsByDefault()
    {
        /** @var \Mockery\MockInterface|Rbac $rbac */
        $rbac = \Mockery::mock(Rbac::class);

        $rbac->shouldReceive('isGranted')
            ->with('my_role', 'foo')
            ->andReturn(false)
            ->once();

        $rbac->shouldReceive('isGranted')
            ->with('my_role', 'bar')
            ->andReturn(true)
            ->once();

        $rbac->shouldReceive('isGranted')
            ->with('my_role', 'baz')
            ->never();

        $assertion = new DefaultAssertion();
        $assertion->setPermission(['foo', 'bar', 'baz']);
        $assertion->setRole('my_role');

        $this->assertTrue(
            $assertion->assert($rbac),
            DefaultAssertion::class . ' did not assert true'
        );
    }

    /**
     * @test
     */
    public function testItShouldFailAssertionWhenNoPermissionsAllowed()
    {
        /** @var \Mockery\MockInterface|Rbac $rbac */
        $rbac = \Mockery::mock(Rbac::class);

        $rbac->shouldReceive('isGranted')
            ->andReturn(false)
            ->byDefault();

        $assertion = new DefaultAssertion();
        $assertion->setPermission(['foo', 'bar', 'baz']);
        $assertion->setRole('my_role');

        $this->assertFalse(
            $assertion->assert($rbac),
            DefaultAssertion::class . ' asserted allowed for role'
        );
    }
}
