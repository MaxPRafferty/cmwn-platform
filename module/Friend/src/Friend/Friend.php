<?php

namespace Friend;

use Feed\FeedableTrait;
use Feed\FeedInterface;
use User\User;
use User\UserInterface;

/**
 * Class Friend
 * @package Friend
 */
class Friend extends User implements FriendInterface
{
    use FriendTrait, FeedableTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        $array = parent::getArrayCopy();
        $array['friend_id']     = $this->getUserId();
        $array['friend_status'] = $this->getFriendStatus();
        unset($array['user_id']);
        return $array;
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): UserInterface
    {
        parent::exchangeArray($array);
        $this->setFriendStatus($array['friend_status'] ?? null);
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getFeedMessage(): string
    {
        return 'Friendship Made';
    }

    /**
     * @inheritdoc
     */
    public function getFeedMeta(): array
    {
        return ['users' => ['friend_id' => $this->getUserId()]];
    }

    /**
     * @inheritdoc
     */
    public function getFeedVisiblity(): int
    {
        return FeedInterface::VISIBILITY_FRIENDS;
    }

    /**
     * @inheritdoc
     */
    public function getFeedType(): string
    {
        return FeedInterface::TYPE_FRIEND;
    }

    /**
     * @inheritdoc
     */
    public function getFeedTitle(): string
    {
        return 'You are now friends with';
    }

    /**
     * @inheritdoc
     */
    public function getFeedPriority(): string
    {
        return '15';
    }
}
