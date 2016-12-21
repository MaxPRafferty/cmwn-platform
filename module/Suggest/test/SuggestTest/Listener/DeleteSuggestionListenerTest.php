<?php

namespace SuggestTest\Listener;

use Friend\Service\FriendServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\Listener\DeleteSuggestionListener;
use Suggest\Service\SuggestedServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Class DeleteSuggestionListenerTest
 *
 * @group Suggest
 * @group Listener
 * @group User
 * @group UserService
 */
class DeleteSuggestionListenerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface | SuggestedServiceInterface
     */
    protected $suggestedService;

    /**
     * @var \Mockery\MockInterface | FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var DeleteSuggestionListener
     */
    protected $listener;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->suggestedService = \Mockery::mock(SuggestedServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpListener()
    {
        $this->listener = new DeleteSuggestionListener($this->suggestedService);
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteSuggestion()
    {
        $event = new Event();
        $event->setName('attach.friend.post');
        $event->setTarget(FriendServiceInterface::class);
        $event->setParam('user', 'english_student');
        $event->setParam('friend', 'math_student');

        $eventManager = new EventManager();
        $eventManager->attach('attach.friend.post', [$this->listener, 'deleteSuggestionIfFriend']);

        $this->suggestedService
            ->shouldReceive('deleteSuggestionForUser')
            ->with('english_student', 'math_student')
            ->once();

        $response = $eventManager->triggerEvent($event);
        $this->assertFalse($response->stopped());
    }
}
