<?php

namespace SecurityTest\Rule\Provider;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\GuestUser;
use Security\Rule\Provider\RoleProvider;
use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Test RoleProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RoleProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldProvideCorrectNameAndValue()
    {
        /** @var \Mockery\MockInterface|AuthenticationServiceInterface $authService */
        $authService = \Mockery::mock(AuthenticationServiceInterface::class);
        $provider    = new RoleProvider($authService);
        $user        = new GuestUser();

        $authService->shouldReceive('getIdentity')->andReturn($user)->byDefault();
        $this->assertEquals(
            'active_role',
            $provider->getName(),
            RoleProvider::class . ' is not providing the correct name'
        );

        $this->assertEquals(
            GuestUser::ROLE_GUEST,
            $provider->getValue(),
            RoleProvider::class . ' is not providing the correct value'
        );
    }
}
