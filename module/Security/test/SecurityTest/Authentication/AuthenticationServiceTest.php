<?php

namespace SecurityTest\Authentication;

use PHPUnit\Framework\TestCase;
use Security\Authentication\AuthAdapter;
use Security\Authentication\AuthenticationService;
use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\GuestUser;
use Security\SecurityUser;
use Zend\Authentication\Result;
use Zend\Authentication\Storage\NonPersistent;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Test AuthenticationServiceTest
 *
 * @group Authentication
 * @group User
 * @group Security
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthenticationServiceTest extends TestCase
{
    /**
     * @var NonPersistent
     */
    protected $storage;

    /**
     * @var \Mockery\MockInterface|AuthAdapter
     */
    protected $authAdapter;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpAuthenticationService()
    {
        $this->calledEvents = [];
        $this->authService  = new AuthenticationService(
            new EventManager(),
            $this->storage,
            $this->authAdapter
        );
        $this->authService->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpAuthAdapter()
    {
        $this->authAdapter = \Mockery::mock(AuthAdapter::class);
    }

    /**
     * @before
     */
    public function setUpStorage()
    {
        $this->storage = new NonPersistent();
    }

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams(),
        ];
    }

    /**
     * @test
     */
    public function testItShouldReturnCorrectIdentity()
    {
        $user = new SecurityUser();
        $this->storage->write($user);

        $this->assertSame($user, $this->authService->getIdentity());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNeedsToChangePassword()
    {
        $user = new ChangePasswordUser();
        $this->storage->write($user);

        $this->expectException(ChangePasswordException::class);
        $this->authService->getIdentity();
    }

    /**
     * @test
     */
    public function testItShouldReturnGuestUserIfNoIdentitySet()
    {
        $this->assertEquals(
            new GuestUser(),
            $this->authService->getIdentity(),
            AuthenticationService::class . ' did not return a GuestUser when no user is set'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCorrectEventsOnSuccessfulLogin()
    {
        $user   = new SecurityUser();
        $result = new Result(Result::SUCCESS, $user);

        $this->authAdapter->shouldReceive('authenticate')
            ->andReturn($result)
            ->once();

        $this->authService->authenticate();

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AuthenticationService::class . ' did not call the correct number of events on success'
        );

        $this->assertEquals(
            [
                'name'   => 'login.success',
                'target' => $user,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCorrectEventsOnLoginNotFound()
    {
        $user   = new GuestUser();
        $result = new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $user);

        $this->authAdapter->shouldReceive('authenticate')
            ->andReturn($result)
            ->once();

        $this->authService->authenticate();

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AuthenticationService::class . ' did not call the correct number of events on success'
        );

        $this->assertEquals(
            [
                'name'   => 'login.not.found',
                'target' => $user,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCorrectEventsOnLoginInvalid()
    {
        $user   = new GuestUser();
        $result = new Result(Result::FAILURE_CREDENTIAL_INVALID, $user);

        $this->authAdapter->shouldReceive('authenticate')
            ->andReturn($result)
            ->once();

        $this->authService->authenticate();

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AuthenticationService::class . ' did not call the correct number of events on success'
        );

        $this->assertEquals(
            [
                'name'   => 'login.invalid',
                'target' => $user,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCorrectEventsOnLoginExpired()
    {
        $user   = new GuestUser();
        $result = new Result(Result::FAILURE_UNCATEGORIZED, $user);

        $this->authAdapter->shouldReceive('authenticate')
            ->andReturn($result)
            ->once();

        $this->authService->authenticate();

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AuthenticationService::class . ' did not call the correct number of events on success'
        );

        $this->assertEquals(
            [
                'name'   => 'login.expired',
                'target' => $user,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCorrectEventsOnLoginFailure()
    {
        $user   = new GuestUser();
        $result = new Result(Result::FAILURE, $user);

        $this->authAdapter->shouldReceive('authenticate')
            ->andReturn($result)
            ->once();

        $this->authService->authenticate();

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AuthenticationService::class . ' did not call the correct number of events on success'
        );

        $this->assertEquals(
            [
                'name'   => 'login.fatal.error',
                'target' => $user,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCorrectEventsOnLoginAmbiguous()
    {
        $user   = new GuestUser();
        $result = new Result(Result::FAILURE_IDENTITY_AMBIGUOUS, $user);

        $this->authAdapter->shouldReceive('authenticate')
            ->andReturn($result)
            ->once();

        $this->authService->authenticate();

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AuthenticationService::class . ' did not call the correct number of events on success'
        );

        $this->assertEquals(
            [
                'name'   => 'login.fatal.error',
                'target' => $user,
                'params' => []
            ],
            $this->calledEvents[0]
        );
    }
}
