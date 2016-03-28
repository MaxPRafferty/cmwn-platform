<?php

namespace ForgotTest\Delegator;

use Application\Exception\NotFoundException;
use Forgot\Delegator\ForgotServiceDelegator;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\SecurityUser;
use Zend\EventManager\Event;

/**
 * Exception ForgotServiceDelegatorTest
 */
class ForgotServiceDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Forgot\Service\ForgotService
     */
    protected $forgotService;

    /**
     * @var ForgotServiceDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams()
        ];
    }

    /**
     * @before
     */
    public function setUpForgotService()
    {
        $this->forgotService = \Mockery::mock('\Forgot\Service\ForgotService');
        $this->forgotService->shouldReceive('generateCode')
            ->andReturn('foobar')
            ->byDefault();
    }
    
    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->delegator = new ForgotServiceDelegator($this->forgotService);
        $this->delegator->getEventManager()->clearListeners('forgot.password');
        $this->delegator->getEventManager()->clearListeners('forgot.password.post');
        $this->delegator->getEventManager()->clearListeners('forgot.password.errors');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    public function testItShouldCallSaveForgotPasswordWhenNullCode()
    {
        $user = new SecurityUser();
        $user->setEmail('chuck@manchuck.com');

        $this->forgotService->shouldReceive('saveForgotPassword')
            ->once()
            ->with('chuck@manchuck.com', 'foobar')
            ->andReturn($user);

        $this->assertTrue(
            $this->delegator->saveForgotPassword('chuck@manchuck.com'),
            'Delegator MUST return true always'
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'forgot.password',
                'target' => $this->forgotService,
                'params' => ['email' => 'chuck@manchuck.com', 'code' => 'foobar']
            ],
            $this->calledEvents[0],
            'Delegator did not create correct forgot.password event'
        );

        $this->assertEquals(
            [
                'name'   => 'forgot.password.post',
                'target' => $this->forgotService,
                'params' => ['user' => $user, 'email' => 'chuck@manchuck.com', 'code' => 'foobar']
            ],
            $this->calledEvents[1],
            'Delegator did not create correct forgot.password.post event'
        );
    }

    public function testItShouldCallSaveForgotPasswordWhenCode()
    {
        $user = new SecurityUser();
        $user->setEmail('chuck@manchuck.com');

        $this->forgotService->shouldReceive('saveForgotPassword')
            ->once()
            ->with('chuck@manchuck.com', 'bazbat')
            ->andReturn($user);

        $this->assertTrue(
            $this->delegator->saveForgotPassword('chuck@manchuck.com', 'bazbat'),
            'Delegator MUST return true always'
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'forgot.password',
                'target' => $this->forgotService,
                'params' => ['email' => 'chuck@manchuck.com', 'code' => 'bazbat']
            ],
            $this->calledEvents[0],
            'Delegator did not create correct forgot.password event'
        );

        $this->assertEquals(
            [
                'name'   => 'forgot.password.post',
                'target' => $this->forgotService,
                'params' => ['user' => $user, 'email' => 'chuck@manchuck.com', 'code' => 'bazbat']
            ],
            $this->calledEvents[1],
            'Delegator did not create correct forgot.password.post event'
        );
    }

    public function testItShouldNotCallForgotPasswordWhenEventStops()
    {
        $user = new SecurityUser();
        $user->setEmail('chuck@manchuck.com');

        $this->delegator->getEventManager()->attach('forgot.password', function (Event $event) {
            $event->stopPropagation(true);
            return true;
        });

        $this->forgotService->shouldReceive('saveForgotPassword')
            ->never();

        $this->assertTrue(
            $this->delegator->saveForgotPassword('chuck@manchuck.com'),
            'Delegator MUST return true always'
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'forgot.password',
                'target' => $this->forgotService,
                'params' => ['email' => 'chuck@manchuck.com', 'code' => 'foobar']
            ],
            $this->calledEvents[0],
            'Delegator did not create correct forgot.password event'
        );
    }

    public function testItShouldCallErrorEventWhenNotFoundExceptionThrown()
    {
        $user = new SecurityUser();
        $user->setEmail('chuck@manchuck.com');
        $exception = new NotFoundException();

        $this->forgotService->shouldReceive('saveForgotPassword')
            ->once()
            ->with('chuck@manchuck.com', 'foobar')
            ->andThrow($exception);

        $this->assertTrue(
            $this->delegator->saveForgotPassword('chuck@manchuck.com'),
            'Delegator MUST return true always'
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'forgot.password',
                'target' => $this->forgotService,
                'params' => ['email' => 'chuck@manchuck.com', 'code' => 'foobar']
            ],
            $this->calledEvents[0],
            'Delegator did not create correct forgot.password event'
        );

        $this->assertEquals(
            [
                'name'   => 'forgot.password.error',
                'target' => $this->forgotService,
                'params' => ['email' => 'chuck@manchuck.com', 'code' => 'foobar', 'exception' => $exception, ]
            ],
            $this->calledEvents[1],
            'Delegator did not create correct forgot.password.error event with NotFoundException'
        );
    }

    public function testItShouldCallErrorEventWhenExceptionThrown()
    {
        $user = new SecurityUser();
        $user->setEmail('chuck@manchuck.com');
        $exception = new \Exception();

        $this->forgotService->shouldReceive('saveForgotPassword')
            ->once()
            ->with('chuck@manchuck.com', 'foobar')
            ->andThrow($exception);

        $this->assertTrue(
            $this->delegator->saveForgotPassword('chuck@manchuck.com'),
            'Delegator MUST return true always'
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'forgot.password',
                'target' => $this->forgotService,
                'params' => ['email' => 'chuck@manchuck.com', 'code' => 'foobar']
            ],
            $this->calledEvents[0],
            'Delegator did not create correct forgot.password event'
        );

        $this->assertEquals(
            [
                'name'   => 'forgot.password.error',
                'target' => $this->forgotService,
                'params' => ['email' => 'chuck@manchuck.com', 'code' => 'foobar', 'exception' => $exception, ]
            ],
            $this->calledEvents[1],
            'Delegator did not create correct forgot.password.error event with NotFoundException'
        );
    }
}
