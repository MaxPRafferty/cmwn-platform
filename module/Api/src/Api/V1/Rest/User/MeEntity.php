<?php

namespace Api\V1\Rest\User;

use Api\TokenEntityInterface;
use User\UserInterface;

/**
 * Represents the authenticated user through the API
 * @todo remove token with JWT switch
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
     * @inheritdoc
     */
    public function canFriend()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getFriendStatus()
    {
        return static::CANT_FRIEND;
    }

    /**
     * @inheritdoc
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        return array_merge(
            parent::getArrayCopy(),
            ['token' => $this->token]
        );
    }

    /**
     * @inheritdoc
     */
    public function getEntityType()
    {
        return 'me';
    }
}
