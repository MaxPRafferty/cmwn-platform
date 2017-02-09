<?php

namespace SuggestTest\Delegator;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\Delegator\SuggestedServiceDelegator;
use Suggest\Suggestion;
use User\Child;
use User\UserInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Class SuggestedServiceDelegatorTest
 *
 * @group Delegator
 * @group Service
 * @group Suggest
 */
class SuggestedServiceDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface| \Suggest\Service\SuggestedService
     */
    protected $suggestedService;

    /**
     * @var SuggestedServiceDelegator
     */
    protected $delegator;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var Suggestion
     */
    protected $suggestion;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Child(['user_id' => 'english_student']);
    }

    /**
     * @before
     */
    public function setUpSuggestion()
    {
        $this->suggestion = new Suggestion(['user_id' => 'math_student']);
    }

    /**
     * @before
     */
    public function setUpSuggestedService()
    {
        $this->suggestedService = \Mockery::mock('\Suggest\Service\SuggestedService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $events = new EventManager();
        $this->delegator = new SuggestedServiceDelegator($this->suggestedService, $events);
        $this->delegator->getEventManager()->clearListeners('fetch.suggested.friends');
        $this->delegator->getEventManager()->clearListeners('fetch.suggested.friends.post');
        $this->delegator->getEventManager()->clearListeners('attach.suggested.friends');
        $this->delegator->getEventManager()->clearListeners('attach.suggested.friends.post');
        $this->delegator->getEventManager()->clearListeners('delete.suggestion');
        $this->delegator->getEventManager()->clearListeners('delete.suggestion.post');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
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
    public function testItShouldCallFetchSuggestedFriends()
    {
        $resultSet = new ResultSet();
        $this->suggestedService->shouldReceive('fetchSuggestedFriendsForUser')
            ->andReturn($resultSet);
        $this->delegator->fetchSuggestedFriendsForUser($this->user);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.suggested.friends',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.suggested.friends.post',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchSuggestedFriendsWhenEventStops()
    {
        $this->suggestedService->shouldReceive('fetchSuggestedFriends')
            ->never();
        $this->delegator->getEventManager()->attach('fetch.suggested.friends', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });
        $this->assertEquals(['foo' => 'bar'], $this->delegator->fetchSuggestedFriendsForUser($this->user));
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.suggested.friends',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'where' => new Where(), 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallAttachSuggestedFriendForUser()
    {
        $this->suggestedService->shouldReceive('attachSuggestedFriendForUser')
            ->andReturn(true);
        $this->delegator->attachSuggestedFriendForUser($this->user, $this->suggestion);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.suggested.friends',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'suggestion' => $this->suggestion],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'attach.suggested.friends.post',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'suggestion' => $this->suggestion],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachSuggestedFriendForUserWhenEventStops()
    {
        $this->suggestedService->shouldReceive('attachSuggestedFriendForUser')
            ->never();
        $this->delegator->getEventManager()->attach('attach.suggested.friends', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });
        $this->assertEquals(
            ['foo' => 'bar'],
            $this->delegator->attachSuggestedFriendForUser($this->user, $this->suggestion)
        );
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.suggested.friends',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'suggestion' => $this->suggestion],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteSuggestionForUser()
    {
        $this->suggestedService->shouldReceive('deleteSuggestionForUser')
            ->andReturn(true);
        $this->delegator->deleteSuggestionForUser($this->user, $this->suggestion);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.suggestion',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'suggestion' => $this->suggestion],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'delete.suggestion.post',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'suggestion' => $this->suggestion],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteSuggestionWhenEventPrevents()
    {
        $this->suggestedService->shouldReceive('deleteSuggestionForUser')
            ->never();
        $this->delegator->getEventManager()->attach('delete.suggestion', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });
        $this->assertEquals(
            ['foo' => 'bar'],
            $this->delegator->deleteSuggestionForUser($this->user, $this->suggestion)
        );
        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.suggestion',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user, 'suggestion' => $this->suggestion],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteAllSuggestionsForUser()
    {
        $this->suggestedService->shouldReceive('deleteAllSuggestionsForUser')
            ->andReturn(true);
        $this->delegator->deleteAllSuggestionsForUser($this->user);
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.all.suggestions',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'delete.all.suggestions.post',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteAllSuggestionsForUserWhenStopped()
    {
        $this->suggestedService->shouldReceive('deleteAllSuggestionsForUser')
            ->never();

        $this->delegator->getEventManager()->attach('delete.all.suggestions', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });

        $this->assertEquals(
            ['foo' => 'bar'],
            $this->delegator->deleteAllSuggestionsForUser($this->user, $this->suggestion)
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.all.suggestions',
                'target' => $this->suggestedService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[0]
        );
    }
}
