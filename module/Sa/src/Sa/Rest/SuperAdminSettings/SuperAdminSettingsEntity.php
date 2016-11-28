<?php

namespace Sa\Rest\SuperAdminSettings;

use Api\Links\UserLink;
use ZF\Hal\Entity;
use ZF\Hal\Link\LinkCollection;

/**
 * Class SuperAdminSettingsEntity
 * @package Api\SuperAdminSettings
 */
class SuperAdminSettingsEntity extends Entity
{
    /**
     * @return \ZF\Hal\Link\LinkCollection
     */
    public function getLinks()
    {
        $userLink = new UserLink();
        $userLink->setProps(['label' => 'Manage Users']);
        $this->setLinks(new LinkCollection());
        $this->links->add($userLink);
        return $this->links;
    }
}
