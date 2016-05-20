<?php

namespace Api\V1\Rest\User;

use Api\Links\FlipLink;
use Api\Links\FriendLink;
use Api\Links\GameLink;
use Api\Links\PasswordLink;
use Api\Links\UserFlipLink;
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

        if (!$links->has('game')) {
            $links->add(new GameLink());
        }

        if (!$links->has('flip')) {
            $links->add(new FlipLink());
        }

        if (!$links->has('password') && !empty($this->getUserId())) {
            $links->add(new PasswordLink($this->getUserId()));
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

        if (!$links->has('user_flips') && !empty($this->getUserId())) {
            $links->add(new UserFlipLink($this->getUserId()));
        }

        if (!$links->has('friend') && !empty($this->getUserId())) {
            $links->add(new FriendLink($this->getUserId()));
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
