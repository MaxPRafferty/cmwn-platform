<?php

namespace Api\V1\Rest\User;

use Api\TokenEntityInterface;
use User\UserInterface;

/**
 * Represents the authenticated user through the API
 *
 * @SWG\Definition(
 *     description="A MeEntity represents the current authorized user in the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links to resources the user has access too",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/UserEntity")
 *     }
 * )
 * @todo remove token with JWT switch
 * @todo expand swagger links
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
