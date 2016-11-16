<?php
namespace IntegrationTest\Service;

use Friend\FriendInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;
use User\Child;
use User\UserInterface;
use Zend\Paginator\Paginator;

/**
 * Test FriendServiceTest
 *
 * @group Friend
 * @group IntegrationTest
 * @group FriendService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FriendServiceTest extends TestCase
{
    /**
     * @var FriendServiceInterface
     */
    protected $friendService;
    /**
     * @var UserInterface|Child
     */
    protected $user;
    /**
     * @var UserInterface|Child
     */
    protected $friend;
    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = TestHelper::getDbServiceManager()->get(FriendServiceInterface::class);
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
    public function setUpFriend()
    {
        $this->friend = new Child(['user_id' => 'math_student']);
    }
    /**
     * @test
     */
    public function testItShouldAttachFriendsWithCorrectStatues()
    {
        try {
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
            $this->fail('This test requires that math_student and english_student are not friends');
        } catch (NotFriendsException $nf) {
            //noop
        }
        $this->friendService->attachFriendToUser($this->user, $this->friend);
        $this->assertEquals(
            FriendInterface::PENDING,
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            'When attaching friends, the 1st step is that they are pending'
        );
        $this->friendService->attachFriendToUser($this->user, $this->friend);
        $this->assertEquals(
            FriendInterface::PENDING,
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            'When the same user is asking to be friends, the status must stay pending'
        );
        $this->friendService->attachFriendToUser($this->friend, $this->user);
        $this->assertEquals(
            FriendInterface::FRIEND,
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            'When friend accepts request, status must change to friend'
        );
    }
    /**
     * @test
     */
    public function testItShouldAllowRequestedFriendToNotAccept()
    {
        try {
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
            $this->fail('This test requires that math_student and english_student are not friends');
        } catch (NotFriendsException $nf) {
            //noop
        }
        $this->friendService->attachFriendToUser($this->user, $this->friend);
        $this->assertEquals(
            FriendInterface::PENDING,
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            'When attaching friends, the 1st step is that they are pending'
        );
        $this->friendService->detachFriendFromUser($this->friend, $this->user);
        try {
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
            $this->fail('This test requires that math_student and english_student are not friends');
        } catch (NotFriendsException $nf) {
            //noop
        }
    }
    /**
     * @test
     */
    public function testItShouldAllowRequestingFriendToCancelRequest()
    {
        try {
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
        } catch (NotFriendsException $nf) {
            //noop
        }
        $this->friendService->attachFriendToUser($this->user, $this->friend);
        $this->assertEquals(
            FriendInterface::PENDING,
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend),
            'When attaching friends, the 1st step is that they are pending'
        );
        $this->friendService->detachFriendFromUser($this->user, $this->friend);
        try {
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
        } catch (NotFriendsException $nf) {
            //noop
        }
    }
    /**
     * @test
     */
    public function testItShouldReturnCorrectFriendsWhenFetchingAllFriendsByUserId()
    {
        try {
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
        } catch (NotFriendsException $nf) {
            //noop
        }
        $this->friendService->attachFriendToUser($this->user, $this->friend);
        $friendList = new Paginator($this->friendService->fetchFriendsForUser($this->user));
        $actualId   = [];
        /** @var UserInterface $friend */
        foreach ($friendList as $friend) {
            $this->assertInstanceOf(UserInterface::class, $friend);
            array_push($actualId, $friend->getUserId());
        }
        $this->assertEquals(
            [$this->friend->getUserId()],
            $actualId,
            'Friend List did not return correct number of friends'
        );
    }
    /**
     * @test
     */
    public function testItShouldReturnCorrectFriendsWhenFetchingAllFriendsByFriendId()
    {
        try {
            $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
        } catch (NotFriendsException $nf) {
            //noop
        }
        $this->friendService->attachFriendToUser($this->user, $this->friend);
        $friendList = new Paginator($this->friendService->fetchFriendsForUser($this->friend));
        $actualId   = [];
        /** @var UserInterface $friend */
        foreach ($friendList as $friend) {
            $this->assertInstanceOf(UserInterface::class, $friend);
            array_push($actualId, $friend->getUserId());
        }
        $this->assertEquals(
            [$this->user->getUserId()],
            $actualId,
            'Friend List did not return correct number of friends'
        );
    }
}
