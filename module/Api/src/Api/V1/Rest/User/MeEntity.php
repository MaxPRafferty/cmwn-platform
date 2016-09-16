<?php

namespace Api\V1\Rest\User;

use Api\Links\FeedLink;
use Api\Links\FlipLink;
use Api\Links\FriendLink;
use Api\Links\GameLink;
use Api\Links\PasswordLink;
use Api\Links\SaveGameLink;
use Api\Links\SkribbleLink;
use Api\Links\UserLink;
use Api\Links\UserNameLink;
use Api\TokenEntityInterface;
use User\UserInterface;
use ZF\Hal\Link\LinkCollection;

/**
 * Class MeEntity
 * @package Api\V1\Rest\User
 */
class MeEntity extends UserEntity implements TokenEntityInterface
{
    /**
     * @var string|null
     */
    protected $token = null;

    /**
     * MeEntity constructor.
     * @param array $user
     * @param null $token
     */
    public function __construct($user, $token = null)
    {
        $userData = $user instanceof UserInterface ? $user->getArrayCopy() : $user;

        if ($token !== null) {
            $this->setToken($token);
        }

        parent::__construct($userData);
    }


    /**
     * Me Entities cannot friend themselves
     *
     * @return bool
     */
    public function canFriend()
    {
        return false;
    }

    /**
     * Me Entities cannot friend themselves
     *
     * @return string
     */
    public function getFriendStatus()
    {
        return static::CANT_FRIEND;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getArrayCopy()
    {
        return array_merge(
            parent::getArrayCopy(),
            ['token' => $this->token]
        );
    }

    /**
     * @param LinkCollection $links
     */
    protected function injectLinks(LinkCollection $links)
    {
        parent::injectLinks($links);

        if (!$links->has('user')) {
            $links->add(new UserLink());
        }

        if (!$links->has('games')) {
            $links->add(new GameLink());
        }

        if (!$links->has('flip')) {
            $links->add(new FlipLink());
        }

        if (!$links->has('password') && !empty($this->getUserId())) {
            $links->add(new PasswordLink($this->getUserId()));
        }

        if (!$links->has('save_game') && !empty($this->getUserId())) {
            $links->add(new SaveGameLink($this->getUserId()));
        }

        $this->injectChildLinks($links);
    }

    /**
     * @param LinkCollection $links
     */
    protected function injectChildLinks(LinkCollection $links)
    {
        if ($this->getType() !== UserInterface::TYPE_CHILD) {
            return;
        }

        if (!$links->has('user_name')) {
            $links->add(new UserNameLink());
        }

        if (!$links->has('friend') && !empty($this->getUserId())) {
            $links->add(new FriendLink($this->getUserId()));
        }

        if (!$links->has('skribbles') && !empty($this->getUserId())) {
            $links->add(new SkribbleLink($this->getUserId()));
        }

        if (!$links->has('feed') && !empty($this->getUserId())) {
            $links->add(new FeedLink($this->getUserId()));
        }
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return 'me';
    }
}
