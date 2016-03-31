<?php

namespace ForgotTest\Service;

use Application\Exception\NotFoundException;
use Forgot\Service\ForgotService;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\SecurityUser;

/**
 * Exception ForgotServiceTest
 */
class ForgotServiceTest extends TestCase
{
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
    public function setUpSecurityService()
    {
        $this->securityService = \Mockery::mock('\Security\Service\SecurityServiceInterface');
    }

    /**
     * @before
     */
    public function setUpForgotService()
    {
        $this->forgotService = new ForgotService($this->securityService);
    }

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
