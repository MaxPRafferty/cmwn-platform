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
use IntegrationTest\DataSets\ArrayDataSet;

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
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/friends.dataset.php');
    }

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
            $status = $this->friendService->fetchFriendStatusForUser($this->user, $this->friend);
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
            ['english_student_1', $this->friend->getUserId()],
            $actualId,
            'Friend List did not return correct number of friends'
        );
    }

    /**
     * @test
     * @ticket CORE-2669
     */
    public function testItShouldHaveCorrectStatusForLeftFriend()
    {
        $friend = new Child(['user_id' => 'english_student_1']);
        $status = $this->friendService->fetchFriendStatusForUser(
            $this->user,
            $friend
        );

        $this->assertEquals(FriendInterface::PENDING, $status);
    }

    /**
     * @test
     * @ticket CORE-2669
     */
    public function testItShouldHaveCorrectStatusForRightFriend()
    {
        $friend = new Child(['user_id' => 'english_student_1']);
        $status = $this->friendService->fetchFriendStatusForUser(
            $friend,
            $this->user
        );

        $this->assertEquals(FriendInterface::REQUESTED, $status);
    }

    /**
     * @test
     * @ticket CORE-2669
     */
    public function testItShouldThrowExceptionWhenNotFriends()
    {
        $this->expectException(NotFriendsException::class);
        $this->friendService->fetchFriendStatusForUser($this->user, new Child(['user_id' => 'english_student_3']));
    }
}
