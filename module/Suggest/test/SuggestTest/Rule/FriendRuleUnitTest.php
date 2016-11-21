<?php

namespace SuggestTest\Rule;

use Friend\NotFriendsException;
use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\Rule\FriendRule;
use Suggest\SuggestionCollection;
use User\Child;
use User\UserInterface;

/**
 * Class FriendRuleUnitTest
 * @package SuggestTest\Rule
 */
class FriendRuleUnitTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface | \Friend\Service\FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var FriendRule
     */
    protected $friendRule;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var SuggestionCollection
     */
    protected $container;

    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = \Mockery::mock('Friend\Service\FriendService');
    }

    /**
     * @before
     */
    public function setUpFriendRule()
    {
        $this->friendRule = new FriendRule($this->friendService);
    }

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
    public function setUpContainer()
    {
        $this->container = new SuggestionCollection();
        $friend = new Child(['user_id' => 'math_student']);
        $this->container[$friend->getUserId()] = $friend;
    }

    /**
     * @test
     */
    public function testItShouldRemoveSuggestionIfFriends()
    {
        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->once();
        $this->friendRule->apply($this->container, $this->user);

        $this->assertFalse($this->container->offsetExists('math_student'));
    }

    /**
     * @test
     */
    public function testItShouldNotRemoveSuggestionIfNotFriends()
    {
        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->andThrow(NotFriendsException::class)
            ->once();
        $this->friendRule->apply($this->container, $this->user);

        $this->assertTrue($this->container->offsetExists('math_student'));
    }
}
