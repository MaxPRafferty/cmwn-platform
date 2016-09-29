<?php

namespace NoticeTest\Listeners;

use Notice\EmailModel\NewUserModel;
use Notice\Listeners\NewUserEmailListener;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\SecurityUser;
use User\Adult;
use User\User;
use Zend\EventManager\Event;

/**
 * Test NewUserListenerTest
 * @package NoticeTest\Listeners
 */
class NewUserListenerTest extends TestCase
{
    /**
     * @var NewUserEmailListener
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
     * @var \Mockery\MockInterface|\Notice\EmailModel\NewUserModel
     */
    protected $emailModel;

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
        $this->emailModel = \Mockery::mock('\Notice\EmailModel\NewUserModel');
        $this->listener = new NewUserEmailListener($this->emailModel);
        $this->listener->setMailService($this->mailService);
    }

    /**
     * @test
     */
    public function testItShouldSendNotificationForAdult()
    {
        $this->message->shouldReceive('setTo')
            ->once()
            ->with('baz@bat.com');

        $this->message->shouldReceive('setSubject')
            ->once()
            ->with('Welcome to Change my world now');

        $this->emailModel->shouldReceive('setTemplate')
            ->once()
            ->with('email/user/new.adult.phtml');

        $this->mailService->shouldReceive('setTemplate')
            ->once()
            ->andReturnUsing(function ($viewModel) {
                $this->assertInstanceOf(
                    NewUserModel::class,
                    $viewModel,
                    'View model was not set correctly'
                );

                return true;
            });

        $this->mailService->shouldReceive('send')
            ->once();

        $user = new Adult(['user_id'=>'foo', 'user_name'=>'bar', 'first_name'=> 'baz', 'email' => 'baz@bat.com']);
        $event = new Event('save.new.user.post');
        $event->setParam('user', $user);

        $this->listener->notify($event);
    }

    /**
     * @test
     */
    public function testItShouldNotSendEmailWhenUserTypeIsNotSet()
    {
        $this->message->shouldReceive('setTo')
            ->never();

        $this->message->shouldReceive('setSubject')
            ->never();

        $this->emailModel->shouldReceive('setTemplate')
            ->never();

        $this->mailService->shouldReceive('setTemplate')
            ->never();

        $this->mailService->shouldReceive('send')
            ->never();

        $user = new SecurityUser(['user_id'=>'foo', 'email' => 'baz@bat.com']);
        $event = new Event('save.new.user.post');
        $event->setParam('user', $user);

        $this->listener->notify($event);
    }

    /**
     * @test
     */
    public function testItShouldNotSendEmailWhenUserInvalid()
    {
        $this->message->shouldReceive('setTo')
            ->never();

        $this->message->shouldReceive('setSubject')
            ->never();

        $this->emailModel->shouldReceive('setTemplate')
            ->never();

        $this->mailService->shouldReceive('setTemplate')
            ->never();

        $this->mailService->shouldReceive('send')
            ->never();

        $event = new Event('save.new.user.post');
        $event->setParam('user', 'foo');

        $this->listener->notify($event);
    }

    /**
     * @test
     */
    public function testItShouldNotSendEmailWhenUserIsChild()
    {
        $this->message->shouldReceive('setTo')
            ->never();

        $this->message->shouldReceive('setSubject')
            ->never();

        $this->emailModel->shouldReceive('setTemplate')
            ->never();

        $this->mailService->shouldReceive('setTemplate')
            ->never();

        $this->mailService->shouldReceive('send')
            ->never();

        $user = new SecurityUser(['user_id'=>'foo', 'email' => 'baz@bat.com']);
        $user->setType(User::TYPE_CHILD);
        $event = new Event('save.new.user.post');
        $event->setParam('user', $user);

        $this->listener->notify($event);
    }
}
