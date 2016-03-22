<?php

namespace Api\V1\Rest\User;

use Api\Links\GameLink;
use Api\Links\GroupLink;
use Api\Links\OrgLink;
use Api\TokenEntityInterface;
use Api\V1\Rest\Org\OrgEntity;
use Security\SecurityUser;
use User\UserInterface;
use ZF\Hal\Collection;
use ZF\MvcAuth\Identity\IdentityInterface;

/**
 * Class MeEntity
 * @package Api\V1\Rest\User
 */
class MeEntity extends UserEntity implements TokenEntityInterface
{
    /**
     * @var Collection
     */
    protected $organizations;

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
        if ($user instanceof IdentityInterface) {
            $user = $user->getAuthenticationIdentity();
        }

        $userData = $user instanceof UserInterface ? $user->getArrayCopy() : $user;

        if ($token !== null) {
            $this->setToken($token);
        }

        if ($user instanceof SecurityUser) {
            $this->addOrganizations($user);
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
            [
                'organizations' => $this->organizations,
                'token' => $this->token
            ]
        );
    }

    /**
     * @param SecurityUser $user
     */
    protected function addOrganizations(SecurityUser $user)
    {
        $orgs = [];
        foreach ($user->getOrganizations() as $key => $org) {
            $orgs[]  = new OrgEntity($org);
            $this->getLinks()->add(new OrgLink($org));
        }

        $this->organizations = new Collection($orgs);
    }

}
