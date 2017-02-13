<?php

namespace ForgotTest\Service;

use Forgot\Service\ForgotService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Security\SecurityUser;

/**
 * Test ForgotServiceTest
 *
 * @group Forgot
 * @group Service
 * @group ForgotService
 */
class ForgotServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Security\Service\SecurityServiceInterface
     */
    protected $securityService;

    /**
     * @var ForgotService
     */
    protected $forgotService;

    /**
     * @before
     */
    public function setUpForgotService()
    {
        $this->forgotService = new ForgotService($this->securityService);
    }

    /**
     * @before
     */
    public function setUpSecurityService()
    {
        $this->securityService = \Mockery::mock('\Security\Service\SecurityServiceInterface');
    }

    /**
     * @test
     */
    public function testItShouldSaveCodeWhenUserFound()
    {
        $user = new SecurityUser();
        $user->setEmail('chuck@manchuck.com');
        $code = $this->forgotService->generateCode();

        $this->assertNotEmpty($code, 'Forgot service did not generate a code');
        $this->securityService->shouldReceive('fetchUserByEmail')
            ->once()
            ->with('chuck@manchuck.com')
            ->andReturn($user);

        $this->securityService->shouldReceive('saveCodeToUser')
            ->once()
            ->with($code, $user);

        $this->assertSame(
            $user,
            $this->forgotService->saveForgotPassword('chuck@manchuck.com', $code),
            'Forgot Service did not return user on success'
        );
    }
}
