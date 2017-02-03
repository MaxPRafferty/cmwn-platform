<?php

namespace Sa\V1\Rest\SuperAdminSettings;

use Api\Links\GameDataLink;
use Api\Links\GameLink;
use Api\Links\GroupLink;
use Api\Links\OrgLink;
use Api\Links\SuperFlagLink;
use Api\Links\UserLink;
use ZF\Hal\Entity;

/**
 * Class SuperAdminSettingsEntity
 * @package Api\SuperAdminSettings
 */
class SuperAdminSettingsEntity extends Entity
{
    /**
     * SuperAdminSettingsEntity Constructor
     */
    public function __construct()
    {
        parent::__construct([]);

        $userLink = new UserLink();
        $userLink->setProps(['label' => 'Manage Users']);

        $gameLink = new GameLink('true');
        $gameLink->setProps(['label' => 'Manage Games']);

        $gameDataLink = new GameDataLink('all-about-you');
        $gameDataLink->setProps(['label' => 'Survey Results']);

        $groupLink = new GroupLink();
        $groupLink->setProps(['label' => 'Manage Groups']);

        $orgLink = new OrgLink();
        $orgLink->setProps(['label' => 'Manage Organizations']);

        $this->getLinks()->add($userLink);
        $this->getLinks()->add($gameLink);
        $this->getLinks()->add($gameDataLink);
        $this->getLinks()->add($groupLink);
        $this->getLinks()->add($orgLink);
    }
}
