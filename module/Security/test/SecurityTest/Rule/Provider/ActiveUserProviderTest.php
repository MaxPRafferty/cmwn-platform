<?php

namespace SecurityTest\Rule\Provider;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\GuestUser;
use Security\Rule\Provider\ActiveUserProvider;
use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Test ActiveUserProviderTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ActiveUserProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function testItShouldProvideTheCorrectValueAndName()
    {
        /** @var \Mockery\MockInterface|AuthenticationServiceInterface $authService */
        $authService = \Mockery::mock(AuthenticationServiceInterface::class);
        $provider    = new ActiveUserProvider($authService);
        $user        = new GuestUser();

        $authService->shouldReceive('getIdentity')->andReturn($user)->once();
        $this->assertEquals(
            'active_user',
            $provider->getName(),
            ActiveUserProvider::class . ' Provides the incorrect name'
        );

        $this->assertEquals(
            $user,
            $provider->getValue(),
            ActiveUserProvider::class . ' Provided the wrong value'
        );
    }
}
