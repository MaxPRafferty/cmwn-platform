<?php

namespace Feed\Listener;

use Feed\Feed;
use Feed\FeedInterface;
use Feed\Service\FeedServiceInterface;
use Feed\Service\FeedUserServiceInterface;
use Feed\UserFeed;
use Flip\FlipInterface;
use Flip\Service\FlipUserServiceInterface;
use Friend\FriendInterface;
use Friend\Service\FriendServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Skribble\Service\SkribbleServiceInterface;
use Skribble\SkribbleInterface;
use User\Child;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class InjectFeedListener
 * @package Feed\Listener
 */
class InjectFeedListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var FeedServiceInterface
     */
    protected $feedService;

    /**
     * @var FeedUserServiceInterface
     */
    protected $feedUserService;

    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * InjectFeedListener constructor.
     * @param FeedServiceInterface $feedService
     * @param FeedUserServiceInterface $feedUserService
     * @param FriendServiceInterface $friendService
     */
    public function __construct(
        FeedServiceInterface $feedService,
        FeedUserServiceInterface $feedUserService,
        FriendServiceInterface $friendService
    ) {
        $this->feedService = $feedService;
        $this->feedUserService = $feedUserService;
        $this->friendService = $friendService;
    }

    /**
     * @param SharedEventManagerInterface $events
     * @codeCoverageIgnore
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            FlipUserServiceInterface::class,
            'attach.flip.post',
            [$this,'injectFlipFeed']
        );

        $this->listeners[] = $events->attach(
            FriendServiceInterface::class,
            'attach.friend.post',
            [$this,'injectFriendFeed']
        );

        $this->listeners[] = $events->attach(
            SkribbleServiceInterface::class,
            ['create.skribble.post', 'update.skribble.post'],
            [$this,'injectSkribbleFeed']
        );
    }

    /**
     * @param Event $event
     */
    public function injectFlipFeed(Event $event)
    {
        $user = $event->getParam('user');
        $flip = $event->getParam('flip');
        $flip = $flip instanceof FlipInterface ? $flip->getFlipId() : $flip;

        $feed = new Feed([
            'type' => FeedInterface::TYPE_FLIP,
            'message' => FeedInterface::FLIP_EARNED,
            'type_version' => 1,
            'title' => 'Flip Earned',
            'visibility' => 2,
            'priority' => 5,
            'meta' => ['flip_id' => $flip]
        ]);

        $this->feedService->createFeed($feed);

        $this->feedUserService->attachFeedForUser($user, new UserFeed($feed->getArrayCopy()));
    }

    /**
     * @param Event $event
     */
    public function injectFriendFeed(Event $event)
    {
        $user = $event->getParam('user');
        $friend = $event->getParam('friend');

        if ($user === null || $friend === null) {
            return;
        }

        $user = !$user instanceof UserInterface ? new Child(['user_id' => $user]) : $user;
        $friend = !$friend instanceof UserInterface ? new Child(['user_id' => $friend]) : $friend;

        if ($this->friendService->fetchFriendStatusForUser($user, $friend) !== FriendInterface::FRIEND) {
            return;
        }

        $feed = new Feed([
            'type' => FeedInterface::TYPE_FRIEND,
            'message' => FeedInterface::FRIENDSHIP_MADE,
            'type_version' => 1,
            'sender' => $friend->getUserId(),
            'title' => 'Friendship Made',
            'visibility' => 2,
            'priority' => 15
        ]);

        $this->feedService->createFeed($feed);

        $userFeed = new UserFeed($feed->getArrayCopy());
        $this->feedUserService->attachFeedForUser($user, $userFeed);

        $userFeed->setSender($user);
        $this->feedUserService->attachFeedForUser($friend, $userFeed);
    }

    /**
     * @param Event $event
     */
    public function injectSkribbleFeed(Event $event)
    {
        $skribble = $event->getParam('skribble');

        if (!$skribble instanceof SkribbleInterface || $skribble->getStatus()!==SkribbleInterface::STATUS_COMPLETE) {
            return;
        }

        $feed = new Feed([
            'type' => FeedInterface::TYPE_SKRIBBLE,
            'message' => FeedInterface::SKRIBBLE_RECEIVED,
            'type_version' => 1,
            'sender' => $skribble->getCreatedBy(),
            'title' => 'Friendship Made',
            'visibility' => 2,
            'priority' => 10,
            'meta' => ['skribble_id' => $skribble->getSkribbleId()]
        ]);

        $this->feedService->createFeed($feed);

        $this->feedUserService->attachFeedForUser($skribble->getCreatedBy(), new UserFeed($feed->getArrayCopy()));
    }
}
