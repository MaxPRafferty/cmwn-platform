<?php

namespace Api\V1\Rest\User;

use Api\Links\GroupLink;
use Api\TokenEntityInterface;
use Security\SecurityUser;
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

        if ($user instanceof SecurityUser) {
            foreach ($user->getGroupTypes() as $groupType) {
                $this->getLinks()->add(new GroupLink($groupType));
            }
        }

        parent::__construct($userData);
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


    public function getEntityType()
    {
        return 'me';
    }
}
