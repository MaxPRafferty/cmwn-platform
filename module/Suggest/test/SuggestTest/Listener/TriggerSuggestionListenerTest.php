<?php

namespace SuggestTest\Listener;

use Job\Service\JobServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Suggest\Engine\SuggestionEngine;
use Suggest\Listener\TriggerSuggestionsListener;
use User\Adult;
use User\Child;
use User\Service\UserServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Class TriggerSuggestionListenerTest
 *
 * @group Suggest
 * @group User
 * @group Listener
 * @group UserService
 */
class TriggerSuggestionListenerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface | SuggestionEngine
     */
    protected $suggestionEngine;

    /**
     * @var \Mockery\MockInterface | JobServiceInterface
     */
    protected $jobService;

    /**
     * @var TriggerSuggestionsListener
     */
    protected $listener;

    /**
     * @before
     */
    public function setUpListener()
    {
        $this->listener = new TriggerSuggestionsListener($this->suggestionEngine, $this->jobService);
    }

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->suggestionEngine = \Mockery::mock(SuggestionEngine::class);
        $this->jobService       = \Mockery::mock(JobServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldTriggerSuggestionsForChild()
    {
        $user  = new Child(['user_id' => 'english_student']);
        $event = new Event();
        $event->setName('save.new.user.post');
        $event->setTarget(UserServiceInterface::class);
        $event->setParam('user', $user);

        $eventManager = new EventManager();
        $eventManager->attach('save.new.user.post', [$this->listener, 'triggerSuggestionJob']);

        $this->suggestionEngine->shouldReceive('exchangeArray')->once();
        $this->jobService->shouldReceive('sendJob')->once();

        $response = $eventManager->triggerEvent($event);
        $this->assertFalse($response->stopped());
    }

    /**
     * @test
     */
    public function testItShouldNotTriggerSuggestionsForAdult()
    {
        $user  = new Adult(['user_id' => 'english_teacher']);
        $event = new Event();
        $event->setName('save.new.user.post');
        $event->setTarget(UserServiceInterface::class);
        $event->setParam('user', $user);

        $eventManager = new EventManager();
        $eventManager->attach('save.new.user.post', [$this->listener, 'triggerSuggestionJob']);

        $this->suggestionEngine->shouldReceive('exchangeArray')->never();
        $this->jobService->shouldReceive('sendJob')->never();

        $response = $eventManager->triggerEvent($event);
        $this->assertFalse($response->stopped());
    }
}
