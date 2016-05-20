<?php

namespace Api\V1\Rest\User;

use Api\Links\FriendLink;
use Api\Links\UserFlipLink;
use Api\Links\UserLink;
use Api\Links\UserNameLink;
use Api\TokenEntityInterface;
use User\UserInterface;

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
     * @return \ZF\Hal\Link\LinkCollection
     */
    public function getLinks()
    {
        $links = parent::getLinks();

        if (!$links->has('user')) {
            $links->add(new UserLink());
        }

        // TODO move to check permissions?
        if ($this->getType() !== UserInterface::TYPE_CHILD) {
            return $links;
        }

        if (!$links->has('user_name')) {
            $links->add(new UserNameLink());
        }

        if (!$links->has('user_flips') && !empty($this->userId)) {
            $links->add(new UserFlipLink($this->getUserId()));
        }

        if (!$links->has('user_flips') && !empty($this->userId)) {
            $links->add(new FriendLink($this->getUserId()));
        }

        return $links;
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return 'me';
    }
}
