<?php

namespace SuggestTest\Rule;

use Friend\FriendInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Suggest\Rule\FriendRule;
use Suggest\SuggestionCollection;
use User\Child;

/**
 * Class FriendRuleUnitTest
 *
 * @group User
 * @group Suggest
 * @group Rule
 * @group UserService
 * @group Friend
 */
class FriendRuleTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Friend\Service\FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var FriendRule
     */
    protected $friendRule;

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
    public function setUpFriendService()
    {
        $this->friendService = \Mockery::mock(FriendServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldRemoveSuggestionIfFriends()
    {
        $currentUser    = new Child(['user_id' => 'current_user']);
        $notFriends1    = new Child(['user_id' => 'not_friends_1']);
        $notFriends2    = new Child(['user_id' => 'not_friends_2']);
        $pendingFriends = new Child(['user_id' => 'pending_friends']);
        $waitingApprove = new Child(['user_id' => 'waiting_approve']);
        $alreadyFriends = new Child(['user_id' => 'already_friends']);

        $collection = new SuggestionCollection();
        $collection->append($notFriends1);
        $collection->append($pendingFriends);
        $collection->append($waitingApprove);
        $collection->append($notFriends2);
        $collection->append($alreadyFriends);

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $notFriends1)
            ->andThrow(new NotFriendsException())
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $pendingFriends)
            ->andReturn(FriendInterface::PENDING)
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $notFriends2)
            ->andThrow(new NotFriendsException())
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $waitingApprove)
            ->andReturn(FriendInterface::REQUESTED)
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $alreadyFriends)
            ->andReturn(FriendInterface::FRIEND)
            ->ordered('friend_rule');

        $this->friendRule->apply($collection, $currentUser);

        $this->assertEquals(
            ['not_friends_1' => $notFriends1, 'not_friends_2' => $notFriends2],
            $collection->getArrayCopy(),
            'Existing friends not removed from collection'
        );
    }
}
