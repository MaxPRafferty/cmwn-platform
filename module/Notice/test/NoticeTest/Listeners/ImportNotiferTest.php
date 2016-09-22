<?php

namespace NoticeTest\Listeners;

use Notice\EmailModel\ImportSuccessModel;
use Notice\Listeners\ImportListener;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\Event;

/**
 * Test ImportNotiferTest
 *
 * @group Import
 * @group Notice
 * @group Mail
 */
class ImportNotiferTest extends TestCase
{
    /**
     * @var ImportListener
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
     * @var \Mockery\MockInterface|\Import\ParserInterface
     */
    protected $parser;

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
        $this->listener = new ImportListener(new ImportSuccessModel([]));
        $this->listener->setMailService($this->mailService);
    }

    /**
     * @before
     */
    public function setUpParser()
    {
        $this->parser = \Mockery::mock('\Import\ParserInterface, \Notice\NotificationAwareInterface');
        $this->parser->shouldReceive('getEmail')
            ->andReturn('chuck@manchuck.com')
            ->byDefault();
    }

    /**
     * @test
     */
    public function testItShouldSendErrorMessageWhenFailed()
    {
        $this->message->shouldReceive('setTo')
            ->once()
            ->with('chuck@manchuck.com');

        $this->message->shouldReceive('setSubject')
            ->once()
            ->with('User import error');


        $this->mailService->shouldReceive('setTemplate')
            ->once()
            ->andReturnUsing(function ($viewModel) {
                $this->assertInstanceOf(
                    'Notice\EmailModel\ImportFailedModel',
                    $viewModel,
                    'View model was not set correctly'
                );

                return true;
            });

        $this->mailService->shouldReceive('send')
            ->once();

        $this->parser->shouldReceive('getErrors')
            ->once()
            ->andReturn(['foo', 'bar']);

        $this->parser->shouldReceive('getWarnings')
            ->once()
            ->andReturn(['baz', 'bat']);

        $event = new Event('nyc.import.excel.error', $this->parser);
        $this->assertEmpty($this->listener->notify($event));
        $this->assertFalse($event->propagationIsStopped(), 'Listener should not stop propagation');
    }

    /**
     * @test
     */
    public function testItShouldSendErrorMessageWhenComplete()
    {
        $this->message->shouldReceive('setTo')
            ->once()
            ->with('chuck@manchuck.com');

        $this->message->shouldReceive('setSubject')
            ->once()
            ->with('User import Success');

        $this->mailService->shouldReceive('setTemplate')
            ->once()
            ->andReturnUsing(function ($viewModel) {
                $this->assertInstanceOf(
                    'Notice\EmailModel\ImportSuccessModel',
                    $viewModel,
                    'View model was not set correctly'
                );

                return true;
            });

        $this->mailService->shouldReceive('send')
            ->once();

        $this->parser->shouldReceive('getErrors')
            ->never();

        $this->parser->shouldReceive('getWarnings')
            ->once()
            ->andReturn(['baz', 'bat']);

        $event = new Event('nyc.import.excel.complete', $this->parser);
        $this->assertEmpty($this->listener->notify($event));
        $this->assertFalse($event->propagationIsStopped(), 'Listener should not stop propagation');
    }
}
