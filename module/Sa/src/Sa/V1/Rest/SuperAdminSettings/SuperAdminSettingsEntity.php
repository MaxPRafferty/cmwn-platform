<?php

namespace Sa\V1\Rest\SuperAdminSettings;

use Api\Links\FlipLink;
use Api\Links\GameDataLink;
use Api\Links\GameLink;
use Api\Links\GroupLink;
use Api\Links\OrgLink;
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

        $gameLink = new GameLink(null, true);
        $gameLink->setProps(['label' => 'Manage Games']);

        $gameDataLink = new GameDataLink('all-about-you');
        $gameDataLink->setProps(['label' => 'Survey Results']);

        $groupLink = new GroupLink();
        $groupLink->setProps(['label' => 'Manage Groups']);

        $orgLink = new OrgLink();
        $orgLink->setProps(['label' => 'Manage Organizations']);

        $flipLink = new FlipLink();
        $flipLink->setProps(['label' => 'Manage Flips']);

        $this->getLinks()->add($userLink);
        $this->getLinks()->add($gameLink);
        $this->getLinks()->add($gameDataLink);
        $this->getLinks()->add($groupLink);
        $this->getLinks()->add($orgLink);
        $this->getLinks()->add($flipLink);
    }
}
