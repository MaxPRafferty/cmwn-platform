<?php

namespace Api\V1\Rest\User;

use Api\Links\UserFlipLink;
use Api\Links\UserNameLink;
use Api\TokenEntityInterface;
use User\Service\UserServiceInterface;
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

        $this->getLinks()->add(new UserNameLink());

        parent::__construct($userData);
    }

    /**
     * @param string $userId
     * @return \User\User
     */
    public function setUserId($userId)
    {
        if (empty($this->userId) && !empty($userId) && $this->getType() === UserInterface::TYPE_CHILD) {
            $this->getLinks()->add(new UserFlipLink($this->getUserId()));
        }

        return parent::setUserId($userId);
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
     * @return string
     */
    public function getEntityType()
    {
        return 'me';
    }
}
