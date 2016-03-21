<?php

namespace Api\V1\Rest\User;

use Api\Links\ForgotLink;
use Api\Links\GameLink;
use Api\Links\GroupLink;
use Api\Links\MeLink;
use Api\Links\OrgLink;
use Api\Links\ProfileLink;
use Api\Links\UserImageLink;
use Api\V1\Rest\Org\OrgEntity;
use Security\SecurityUser;
use User\UserInterface;
use ZF\Hal\Collection;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Link\LinkCollectionAwareInterface;
use ZF\MvcAuth\Identity\IdentityInterface;

/**
 * Class MeEntity
 * @package Api\V1\Rest\User
 */
class MeEntity extends UserEntity implements LinkCollectionAwareInterface
{
    /**
     * @var LinkCollection
     */
    protected $links;

    /**
     * @var Collection
     */
    protected $organizations;

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
            $userData['token'] = $token;
        }

        $this->getLinks()->add(new GameLink());

        if ($user instanceof SecurityUser) {
            $this->addOrganizations($user, $userData);
            foreach ($user->getGroupTypes() as $groupType) {
                $this->getLinks()->add(new GroupLink($groupType));
            }
        }

        parent::__construct($userData);
    }

    /**
     * @return mixed
     */
    public function getArrayCopy()
    {
        return array_merge(
            parent::getArrayCopy(),
            ['organizations' => $this->organizations, 'links' => $this->getLinks()]
        );
    }

    /**
     * @param SecurityUser $user
     */
    protected function addOrganizations(SecurityUser $user)
    {
        $orgs = [];
        foreach ($user->getOrganizations() as $key => $org) {
            $orgs[]  = new OrgEntity($org, 7);
            $this->getLinks()->add(new OrgLink($org));
        }

        $this->organizations = new Collection($orgs);
    }

    /**
     * Set link collection
     *
     * @param  LinkCollection $links
     * @return self
     */
    public function setLinks(LinkCollection $links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     * Get link collection
     *
     * @return LinkCollection
     */
    public function getLinks()
    {
        if (!$this->links instanceof LinkCollection) {
            $this->setLinks(new LinkCollection());
        }

        return $this->links;
    }
}
