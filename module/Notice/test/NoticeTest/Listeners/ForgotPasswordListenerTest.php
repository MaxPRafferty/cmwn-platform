<?php

namespace NoticeTest\Listeners;

use Notice\Listeners\ForgotPasswordListener;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\SecurityUser;
use User\Child;
use Zend\EventManager\Event;

/**
 * Test ForgotPasswordListenerTest
 *
 * @group Notice
 * @group Mail
 */
class ForgotPasswordListenerTest extends TestCase
{
    /**
     * @var ForgotPasswordListener
     */
    protected $listener;

    /**
     * @var \Mockery\MockInterface|\AcMailer\Service\MailService
     */
    protected $mailService;

    /**
     * @var \Mockery\MockInterface|\Zend\Mail\Message
     */
    protected $message;

    /**
     * @before
     */
    public function setUpMessage()
    {
        $this->message = \Mockery::mock('\Zend\Mail\Message');

    }
    /**
     * @before
     */
    public function setUpMailService()
    {
        $this->mailService = \Mockery::mock('\AcMailer\Service\MailService');
        $this->mailService->shouldReceive('getMessage')
            ->andReturn($this->message)
            ->byDefault();
    }

    /**
     * @before
     */
    public function setUpListener()
    {
        $this->listener = new ForgotPasswordListener();
        $this->listener->setMailService($this->mailService);
    }

    /**
     * @test
     */
    public function testItShouldSendMessageOnPost()
    {
        $user = new SecurityUser();
        $user->setType(SecurityUser::TYPE_ADULT);
        $user->setEmail('chuck@manchuck.com');

        $this->message->shouldReceive('setTo')
            ->once()
            ->with('chuck@manchuck.com');

        $this->message->shouldReceive('setSubject')
            ->once()
            ->with('Reset Password Code');


        $this->mailService->shouldReceive('setTemplate')
            ->once()
            ->andReturnUsing(function ($viewModel) {
                $this->assertInstanceOf(
                    'Notice\EmailModel\ForgotEmailModel',
                    $viewModel,
                    'View model was not set correctly'
                );

                return true;
            });

        $this->mailService->shouldReceive('send')
            ->once();

        $event = new Event('forgot.password.post');
        $event->setParam('user', $user);
        $this->assertEmpty(
            $this->listener->notify($event),
            'Forgot Password Notifier MUST NOT return anything'
        );

        $this->assertFalse(
            $event->propagationIsStopped(),
            'Listener should not stop propagation'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSendMessageWhenUserIsChild()
    {
        $user = new Child();
        $user->setEmail('chuck@manchuck.com');

        $this->message->shouldReceive('setTo')->never();
        $this->message->shouldReceive('setSubject')->never();
        $this->mailService->shouldReceive('setTemplate')->never();
        $this->mailService->shouldReceive('send')->never();

        $event = new Event('forgot.password.post');
        $event->setParam('user', $user);
        $this->assertEmpty(
            $this->listener->notify($event),
            'Forgot Password Notifier MUST NOT return anything'
        );

        $this->assertFalse(
            $event->propagationIsStopped(),
            'Listener should not stop propagation'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotSendMessageWhenUserIsNotAUser()
    {

        $this->message->shouldReceive('setTo')->never();
        $this->message->shouldReceive('setSubject')->never();
        $this->mailService->shouldReceive('setTemplate')->never();
        $this->mailService->shouldReceive('send')->never();

        $event = new Event('forgot.password.post');
        $event->setParam('user', 'foobar');
        $this->assertEmpty(
            $this->listener->notify($event),
            'Forgot Password Notifier MUST NOT return anything'
        );

        $this->assertFalse(
            $event->propagationIsStopped(),
            'Listener should not stop propagation'
        );
    }
}
