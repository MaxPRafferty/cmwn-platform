<?php

namespace Api\V1\Rest\User;

use Api\Links\FeedLink;
use Api\Links\FlagLink;
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
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return 'me';
    }
}
