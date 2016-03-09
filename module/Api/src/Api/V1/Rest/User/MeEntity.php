<?php

namespace Api\V1\Rest\User;

use Api\Links\ForgotLink;
use Api\Links\GameLink;
use Api\Links\MeLink;
use Api\Links\OrgLink;
use Api\Links\ProfileLink;
use Api\Links\UserImageLink;
use Api\V1\Rest\Org\OrgEntity;
use User\UserInterface;
use ZF\Hal\Collection;
use ZF\Hal\Entity;
use ZF\MvcAuth\Identity\IdentityInterface;

/**
 * Class MeEntity
 * @package Api\V1\Rest\User
 */
class MeEntity extends Entity
{
    public function __construct($user, $token = null)
    {
        if ($user instanceof IdentityInterface) {
            $user = $user->getAuthenticationIdentity();
        }

        $userData = $user instanceof UserInterface ? $user->getArrayCopy() : $user;

        if ($token !== null) {
            $userData['token'] = $token;
        }

        parent::__construct($userData);
        $this->attachHalLinksToOrg();
        $this->getLinks()->add(new MeLink($user));
        $this->getLinks()->add(new ProfileLink($user));
        $this->getLinks()->add(new ForgotLink());
        $this->getLinks()->add(new GameLink());
        $this->getLinks()->add(new UserImageLink($user));
    }

    protected function attachHalLinksToOrg()
    {
        if (!isset($this->entity['organizations'])) {
            return;
        }

        $orgs = [];
        foreach ($this->entity['organizations'] as $key => $org) {
            $orgs[]  = new OrgEntity($org, 7);
            $this->getLinks()->add(new OrgLink($org));
        }

        $this->entity['organizations'] = new Collection($orgs);
    }
}
